#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Created on Mon Jun 19 11:57:20 2023
Fonctions qui formatte les sorties d'une recherche en ne sélectionnant 
que les clés choisie. Le paramètre de recherche doit être à formatter='True'
@author: Raphaël d'Assignies

"""
from typing import List, Union, Dict

import yaml

from importlib import resources

with resources.open_text('pylegifrance', 'config.yaml') as file:
    config = yaml.safe_load(file)
    
ARTICLE_KEYS = config['article_keys']
ROOT_KEYS = config['root_keys']
SECTION_KEYS = config['section_keys']




def formate_text_response(data,
                          root_keys=ROOT_KEYS,
                          section_keys=SECTION_KEYS,
                          article_keys=ARTICLE_KEYS):
    """
        Extrait les données de ConsultTextResponse model (LegiPart).

        Args:
            data (dict): dict contenant le texte et les méta données recherchés 
            root_keys (Tuple): Liste des clés de la racine
            section_keys (Tuple): Liste des clés pour la section
            article_keys (Tuple): Liste des clés pour les articles.

        Returns:
            Dict: Dictionnaire simplifiée, selon les clés retenues, 
            du dictionnaire initial
    """
    # Check if data is a list and contains more than one item
    if isinstance(data, list):
        if len(data) == 1:
            data = data[0]
    else: raise TypeError("Data must be a list or a list with a single item")

    # Fonction interne pour traiter récursivement les sections et articles
    def process_section(section_data):
        section_result = {}

        # Extraire les données des articles dans la section
        if 'articles' in section_data:
            section_result['articles'] = [
                {key: article[key] for key in article_keys if key in article}
                for article in section_data['articles']
            ]

        # Extraire les données de la section elle-même
        section_result['section_data'] = {key: section_data[key]
                                          for key in section_keys
                                          if key in section_data}

        # Traiter les sous-sections récursivement
        if 'sections' in section_data:
            section_result['subsections'] = [
                process_section(subsection) for subsection
                in section_data['sections'
                                ]
            ]

        return section_result

    # Extraction des métadonnées de la racine
    root_data = {key: data[key] for key in root_keys if key in data}

    # Traitement du contenu principal (sections à la racine)
    content = []
    if 'sections' in data:
        content = [process_section(section) for section in data['sections']]

    # Assembler le résultat final
    return {"root": root_data, "content": content}


def formate_article_response(data: Union[List, Dict], article_keys=ARTICLE_KEYS)-> Dict:
    """
    Extrait les données de the GetArticleResponse model (GetArticle).

    Args:
        data (Dict, List): Dict ou liste de dict contenant les articles et les données associées
        article_keys (Tuple): Liste des clés spécifiques à un article à extraire

    Returns:
        Dict: Dictionnaire simplifiée, selon les clés retenues, 
        du dictionnaire initial
    """

    # Function to process a single item (used for both single items and list elements)
    def formate_article_single(item, article_keys):
        simplified_dict = {}
        article = item.get("article", {})
        for key in article_keys:
            simplified_dict[key] = article.get(key)
        return simplified_dict

    # Check if data is a list and contains more than one item
    if isinstance(data, list):
        if len(data) > 1:
            # If there are multiple items, process each one
            return [formate_article_single(item, article_keys) for item in data]
        elif data:
            # If there's only one item in the list, use that
            data = data[0]


    return formate_article_single(data, article_keys)


def print_legal_hierarchy(legal_list):
    for item in legal_list:
        if 'title_id' in item:
            print(f"Title ID: {item['title_id']}")
            print(f"  Title CID: {item['title_cid']}")
            print(f"  Title: {item['title']}\n")
        elif 'section_id' in item:
            print(f"  Section ID: {item['section_id']}")
            print(f"    Title: {item['title']}\n")
        elif 'extract_id' in item:
            print(f"    Extract ID: {item['extract_id']}")
            print(f"      Number: {item['num']}")
            print(f"      Legal Status: {item['legal_status']}")
            print(f"      Date Version: {item['date_version']}")
            print(f"      Title: {item['title']}")
            print(f"      Values: {item['values']}\n")
            

def print_article(data) : 
    for item in data : 
        extracted_data = {
        'fullSectionTitre': item['article']['fullSectionsTitre'],
        'etat': item['article']['etat'],
        'num':  item['article']['num'],
        'texte': item['article']['texte'][:30]
        }
        print(extracted_data)
        print("----------------------------------")
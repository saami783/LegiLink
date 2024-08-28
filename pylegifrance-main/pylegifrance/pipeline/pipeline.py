#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Classe permettant de gérer un pipeline générique de traitement des résultats
renvoyés par l'API Legifrance. 

@author: Raphael d'Assignies'
"""
from pydantic import BaseModel
from typing import List, Union, Dict
import logging
import json
import os

from pylegifrance.models.consult import GetArticle, LegiPart
from pylegifrance.process.processors import (search_response_DTO,
                                get_article_id, get_text_id)
from pylegifrance.process.formatters import formate_text_response, formate_article_response

import yaml

from importlib import resources

with resources.open_text('pylegifrance', 'config.yaml') as file:
    config = yaml.safe_load(file)



logging_level = config['logging']['level']
logging.basicConfig(level=logging_level, format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)
logger.setLevel(logging_level)




class Pipeline:
    """
    Classe représentant un pipeline de traitement de données.
    Attributs :
        steps (List[PipelineStep]) : Liste des étapes du pipeline.
    """

    def __init__(self, steps):
        self.steps = steps

    def execute(self, data, data_type=""):
        """
        Exécute chaque étape du pipeline, en faisant passer les données
        à travers chacune d'entre elles.

        Args:
            data: Données à traiter par le pipeline.

        Returns:
            Données transformées après être passées à travers
            toutes les étapes du pipeline.
        """
        for step in self.steps:
            data, data_type = step.process(data, data_type)
            logger.debug(f"Type de données de l'étape : {data_type}")

        return data


class PipelineStep:
    """
    Classe de base pour une étape dans le pipeline de traitement.
    """

    def process(self, data, data_type=""):
        """
        Méthode de traitement des données à implémenter par chaque sous-classe.

        Args:
            data: Données à traiter.

        Returns:
            Données transformées.
        """
        raise NotImplementedError


class ExtractSearchResult(PipelineStep):
    """
    Une étape du pipeline pour l'extraction des résultats de recherche.
    """
    #TODO : implémenter l'utilisation de data_type à partir du modèle de réponse
    
    def process(self, data, data_type=""):
        """
        Extrait les résultats de recherche d'une réponse d'API.

        Args:
            data (requests.models.Response):
            La réponse de l'API contenant les résultats de recherche.

        Returns:
            Résultats de recherche extraits.

        Raises:
            ValueError: Si la clé 'results' n'est pas trouvée dans la réponse.
        """
        #TODO : vérifier que data soit une instance de requests.models.Response
        #TODO : gérer l'erreur si la clé results n'est pas présente
        data = search_response_DTO(data)
        return data, "ExtractSearchResult"



class GetArticleId(PipelineStep):
    """
    Une étape du pipeline pour récupérer les identifiants d'articles LEGIARTI.
    """
    
    def process(self, data, data_type=""):
        """
        Génère des modèles GetArticle à partir des données fournies.

        Args:
            data (List[dict]): Une liste de résultats avec des identifiants.

        Returns:
            Une liste d'objets GetArticle (voir models.models).

        Raises:
            TypeError: Si les données fournies ne sont pas 
            dans le format correct pour extraire les identifiants d'articles.
        """

        if data_type == "ExtractSearchResult":
            article_ids = get_article_id(data)
        else:
            raise TypeError("Les données pour extraire les identifiants d'articles"
                            " ne sont pas dans le format correct")

        return article_ids, GetArticle.__name__


class GetTextId(PipelineStep):
    """
    Une étape du pipeline pour récupérer les identifiants de textes LEGITEXT.
    """

    def process(self, data, data_type="") -> Dict:
        """
        Génère des modèles LegiPart à partir des données fournies.

        Args:
            data (List[dict]): Une liste de résultats avec des identifiants.

        Returns:
            Une liste d'objets LegiPart (voir models.models).

        Raises:
            TypeError: Si les données fournies ne sont pas dans le format correct
            pour extraire les identifiants de textes.

        """

        if data_type == "ExtractSearchResult":
            text_id = get_text_id(data)
        else:
             raise TypeError("Les données pour extraire les identifiants de textes ne sont "
                             "pas dans le format correct")

        return text_id, LegiPart.__name__



class CallApiStep(PipelineStep):
    """
    Étape d'appel d'API dans le pipeline.

    Attributs:
        client (LegiHandler): Client pour appeler l'API.
    """

    def __init__(self, client):
        self.client = client

    def process(self, data: Union[BaseModel, List[BaseModel]], data_type=""):
        """
        Appelle l'API LegiFrance en utilisant les modèles (payload).

        Args:
            data (Union[BaseModel, List[BaseModel]]): Modèles Pydantic
            pour générer le payload.

        Raises:
            ValueError: Lève une erreur si le modèle n'est pas un type Pydantic.

        Returns:
            Soit un objet 'requests.models.Response' ou une liste
            d'objets 'requests.models.Response'.
        """

        # Vérifie si 'data' est un modèle Pydantic ou une liste de modèles
        if isinstance(data, BaseModel):
            # Traitement pour un seul modèle Pydantic
            return self._call_api_single(data)
        elif isinstance(data, list) and all(isinstance(item, BaseModel) for item in data):
            # Traitement pour une liste de modèles Pydantic
            return self._call_api_multiple(data)
        else:
            raise ValueError("Les données d'entrée doivent être un modèle Pydantic "
                             "ou une liste de modèles Pydantic")

    def _call_api_single(self, model: BaseModel) -> Dict:
        """
        Appelle l'API avec une seule requête (modèles).

        Args:
            models (List[BaseModel]): Liste de modèles Pydantic utilisés
            pour générer le payload.

        Returns:
            response.content jsonifié du module requests.
        """
        # Logique pour appeler l'API avec un seul modèle
        response = self.client.call_api(
            route=model.Config.route,
            data=model.model_dump(mode='json')
        )

        # Log des informations de la réponse
        logger.debug("---------- call_api_SINGLE -------------")
        logger.debug(f"Appel API vers {model.Config.route} retourné "
                     "code de statut {response.status_code}")
        # logger.debug(f"En-têtes de réponse: {response.headers}")
        logger.debug(f"Corps de réponse: {response.text}")

        return json.loads(response.content.decode('utf-8')), model.Config.model_reponse

    def _call_api_multiple(self, models: List[BaseModel]) -> List:
        """
        Appelle l'API avec une liste de requêtes (modèles).

        Args:
            models (List[BaseModel]): Liste de modèles Pydantic utilisés
            pour générer le payload.

        Returns:
            List[requests.models.Response]: Renvoie une liste d'objets Response
            du module requests.
        """

        # Logique pour appeler l'API avec plusieurs modèles
        responses = []
        for model in models:
            response = self.client.call_api(
                route=model.Config.route,
                data=model.model_dump(mode='json')
            )
            responses.append(json.loads(response.content.decode('utf-8')))

            # Log des informations de la réponse
            logger.debug(f"---------- call_api_MULTIPLE -------------")
            logger.debug(f"Appel API vers {model.Config.route} retourné "
                         "code de statut {response.status_code}")
            # logger.debug(f"En-têtes de réponse: {response.headers}")
            # logger.debug(f"Corps de réponse: {response.text}")

        return responses, models[0].Config.model_reponse


class Formatters(PipelineStep):
    """
      Etape de formattage des résultats
    """
    def __init__(self, model='default'): 
        self.model = 'default'

    def process(self, data, data_type=""):
        """
        Fonction qui appelle une fonction qui formatte un texte ou un article

        Args:
           data (Dict): GetArticleResponse ou ConsultTextResponse.
           data_type (String, optional): GetArticleResponse ou ConsultTextResponse.
    
        Returns:
           Dict: Dictionnaire des résultats reformattés
        """

        if data_type == "GetArticleResponse":
           articles = formate_article_response(data)
           return articles, str(type(articles))

        if data_type == "ConsultTextResponse":
           text = formate_text_response(data, )
           return text, str(type(text))

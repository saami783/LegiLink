from flask import Flask, request, jsonify
from pylegifrance import recherche_CODE, LegiHandler

app = Flask(__name__)

# Configuration de l'API Legifrance
# client = LegiHandler()
# client.set_api_keys(legifrance_api_key="1559207d-b427-4364-a53a-e74f3fb605eb", legifrance_api_secret="73b502c0-172e-448e-a5ae-d7b6333d2ff7")

@app.route('/api/get_article', methods=['POST'])
def recherche_code():
    data = request.json

    code_name = data.get('code_name', 'Code civil')  # Valeur par défaut si non spécifiée
    search = data.get('search', None)
    champ = data.get('champ', 'ALL')  # Valeur par défaut si non spécifiée
    formatter = data.get('formatter', False)  # Valeur par défaut si non spécifiée

    # Configuration de l'API Legifrance
    legifrance_api_key = data.get("legifrance_api_key")
    legifrance_api_secret = data.get("legifrance_api_secret")
    client = LegiHandler()
    client.set_api_keys(legifrance_api_key=legifrance_api_key,
                        legifrance_api_secret=legifrance_api_secret)

    try:
        # Exécuter la recherche
        result = recherche_CODE(code_name=code_name, search=search, champ=champ, formatter=formatter)

        # Renvoie le résultat sous forme JSON
        return jsonify(result)

    except Exception as e:
        # Gestion des erreurs et retour d'un message d'erreur clair
        app.logger.error(f"Erreur lors de la recherche: {e}")
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5001)
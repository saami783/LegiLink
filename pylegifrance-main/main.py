from flask import Flask, request, jsonify
from pylegifrance import recherche_CODE, LegiHandler

app = Flask(__name__)
client = LegiHandler()


@app.route('/api/get_article', methods=['POST'])
def recherche_code():
    data = request.json

    legifrance_api_key = data.get("legifrance_api_key")
    legifrance_api_secret = data.get("legifrance_api_secret")
    code_name = data.get('code_name')
    search = data.get('search')
    champ = data.get('champ')
    formatter = data.get('formatter')

    client.set_api_keys(legifrance_api_key=legifrance_api_key,
                        legifrance_api_secret=legifrance_api_secret)

    app.logger.info(
        f"Recherche avec les paramètres : code_name={code_name}, search={search}, champ={champ}, formatter={formatter}")

    try:
        result = recherche_CODE(code_name=code_name, search=search, champ=champ, formatter=formatter)

        if not result or 'error' in result:
            app.logger.error(f"Erreur lors de la recherche : {result}")
            return jsonify({"error": "La recherche n'a retourné aucun résultat"}), 404

        return jsonify(result)

    except Exception as e:
        app.logger.error(f"Erreur lors de la recherche: {e}")
        return jsonify({"error": str(e)}), 500


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5001)

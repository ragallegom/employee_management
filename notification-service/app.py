from flask import Flask, request, jsonify

app = Flask(__name__)

@app.route('/notify', methods=['POST'])
def notify():
    data = request.json
    print("📨 Notificación recibida:", data)  # visible en logs del contenedor
    return jsonify({"message": "Notificación enviada"}), 200

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8000)

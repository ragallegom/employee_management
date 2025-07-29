from flask import Flask, request, jsonify
from email_sender import send_welcome_email
import traceback

app = Flask(__name__)

@app.route('/notify', methods=['POST'])
def notify():
    data = None
    try:
        data = request.get_json()

        if not data or 'email' not in data or 'name' not in data:
            return jsonify({"error": "Invalid payload"}), 400

        send_welcome_email(data['email'], data['name'])
        return jsonify({"message": "Notificaton Successfully"}), 200

    except Exception as e:
        traceback.print_exc()
        return jsonify({"error": f"Email sending failed: {str(e)}"}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8000)
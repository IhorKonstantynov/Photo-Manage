from flask import Flask, request, jsonify
import tensorflow as tf
import numpy as np

app = Flask(__name__)

@app.route('/status')
def index():
    return 'Service is running successfully.'

@app.route('/vetorembedding', methods=['POST'])
def embeddings():
    path = request.json['path']
    # Load pre-trained model and extract the desired layer for embeddings
    model = tf.keras.applications.VGG16(weights='imagenet', include_top=False)
    layer_name = 'block5_pool'
    layer = model.get_layer(layer_name)
    embedding_model = tf.keras.Model(inputs=model.input, outputs=layer.output)

    # Preprocess and obtain embeddings for two images
    image = tf.keras.preprocessing.image.load_img(path, target_size=(299, 299))
    image = tf.keras.preprocessing.image.img_to_array(image)
    image = tf.keras.applications.vgg16.preprocess_input(image)
    embedding1 = embedding_model.predict(np.expand_dims(image, axis=0)).flatten()
    return {
        'embedding': embedding1.tolist()
    }

if __name__ == "__main__":
    app.run(host='0.0.0.0')
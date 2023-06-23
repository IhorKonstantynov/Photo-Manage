const express = require('express');
const http = require('http');
const socketio = require('socket.io');
const cors = require('cors');
const axios = require('axios');
const request = require('request');
const sharp = require('sharp');
const _ = require('lodash');

require('dotenv').config();

const db = require("./models");

const Photo = db.Photo;

const app = express();

const server = http.createServer(app);

const io = socketio(server, {
  cors: {
    origin: 'https://dashboard.prophotos.ai',
    // origin: "http://localhost:8000",
    origin: "*",
  }
});

app.set('io', io);
// cors - allow origin.
app.use(cors({
  origin: 'https://dashboard.prophotos.ai',
  // origin: "http://localhost:8000",
  origin: "*",
}));
// parse requests of content-type - application/json
app.use(express.json());
// parse requests of content-type - application/x-www-form-urlencoded
app.use(express.urlencoded({ extended: true }));

db.sequelize.sync()
  .then(() => {
    console.log("Synced db.");
  })
  .catch((err) => {
    console.log("Failed to sync db: " + err.message);
  });

app.get("/", (req, res) => {
  res.json({ message: "Welcome to server." });
});

app.use('/image/:token/:filename', (req, res) => {
  const { token, filename } = req.params;
  const { highRes } = req.query;

  const url = `https://api.astria.ai/rails/active_storage/blobs/redirect/${token}/${filename}`;
  if(!highRes || highRes == 0) {
    request({ url, encoding: null }, (err, response, body) => {
      if (err || res.statusCode !== 200) {
        console.log('Error:', err || res.statusCode);
        return;
      }
  
      sharp(body)
        .resize(512, 512)
        .toBuffer()
        .then((data) => {
          res.writeHead(200, { 'Content-Type': 'image/jpeg', 'Content-Length': data.length });
          res.end(data);
        })
        .catch((err) => {
          console.error(err);
          res.status(500).send('Internal Server Error');
        });
    });
  } else {
    req.pipe(request(url)).pipe(res);
  }
});

app.post('/created-tune/:id', (req, res) => {
  console.log("-------------[Created Tune]-------------", req.params.id);
  return res.send('sucess');
})

app.post('/prompt_created/:id/:length/:userId', async (req, res) => {
  const { id, length, userId } = req.params;
  console.log('-------------[Created Prompt]-------------', req.body);
  const photo = await Photo.findByPk(id);
  if (photo && photo.request_id) {
    photo.count += 1;
    await photo.save();
    req.app.get('io').emit('processing', {
      userId,
      id,
      count: photo.count,
      length,
      message: 'Processing AI.'
    });
    if (photo.count == Number(length)) {
      photo.status = 1;
      const response = await axios.get(`${process.env.ASTRIA_DOMAIN}/tunes/${photo.request_id}/prompts?offset=0`, {
        headers: {
          Authorization: `Bearer ${process.env.ASTRIA_KEY}`
        }
      });
      let imageUrls = [];
      imageUrls = _.flatten(response.data.map(data => data.images));
      imageUrls = _.flatten(imageUrls);
      console.log(imageUrls);
      photo.result_urls = JSON.stringify(imageUrls);
      await photo.save();
      let url = `https://dashboard.prophotos.ai/webhook/ready/${photo.id}`;
      if(photo.plan_id != 3 && length == 15) {
        url += '?add=1';
      }
      await axios.get(url);
      req.app.get('io').emit('created_tune', {
        userId,
        id,
        message: 'Successfully generated.'
      });
    }
    return res.send("success");
  }
  return res.status(400).send("bad request");
});

io.on('connection', (socket) => {
  console.log('a user connected');

  socket.on('disconnect', () => {
    console.log('user disconnected');
  });

  socket.on('test', (msg) => {
    console.log('message: ' + msg);
    io.emit('chat message', msg);
  });
});

server.listen(3000, () => {
  console.log('listening on *:3000');
});

#! /bin/sh

mv /tmp/package.json /usr/src/app/
cd /usr/src/app/
npm install express
npx create-react-app camagru
npm run dev
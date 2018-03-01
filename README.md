# chat
📧 Simple chat application built with php and VueJS.

![alt text](https://media1.giphy.com/media/l0HlFlPemJOOhAcpi/giphy.gif "<chat demo>")


## Dev system requirements:
You must have the following intalled in your machine:

- [node](https://nodejs.org/en/)
- [npm](https://www.npmjs.com/get-npm)
- [php](https://www.python.org/downloads/)
- [apache](https://httpd.apache.org/download.cgi)

This project was built using:

| Program       | Version       |
| ------------- |:-------------:|
| node          | 8.9.4         |
| npm           | 5.6.0         |
| php           | 7.1.7         |
| apache        | 2.4.28 (Unix) |


## Get started

## /chat-backend
To host this locally, all you need to do is make a sym link to your localhost web root:
```
ln -s ../chat-backend/ <web_root_directory>
``` 

To test it works you can hit the following URL
```
http://localhost/chat-webapp/api/hello/<your_name>
```

You are good to go. If the link above works, you should be good to go!


## /chat-webapp
To install dependencies:
```
npm i 
```

To start dev server:
```
npm run dev
```
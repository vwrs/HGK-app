HGK-app
===

```sh
$ docker build -t webserver .
$ docker run --name app -d -p 8080:80 webserver
```
→ http://localhost:8080

DocumentRoot: ./html/

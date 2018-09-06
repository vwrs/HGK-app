HGK-app
===

```sh
$ docker build -t webserver .
$ docker run --name app -d -v `pwd`/html:/var/www/html -p 8080:80 webserver
```
→ http://localhost:8080

DocumentRoot: `./html/`

## credentials
ローカルで開発する際は `credentials` ファイルをこのリポジトリの階層に置く．

format:
```
[default]
aws_access_key_id = XXX
aws_secret_access_key = YYY
```

またはaws-cliで設定済みならコピーする．

```sh
$ cp ~/.aws/credentials .
```


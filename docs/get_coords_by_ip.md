# Определяем геолокацию по IP

https://ipinfo.io/pricing

50k requests/month

https://ipinfo.io/account/home?service=google&loginState=create

```sh
curl "ipinfo.io/188.143.207.215?token=<token>"
```

```json

{
  "ip": "188.143.207.215",
  "city": "Saint Petersburg",
  "region": "St.-Petersburg",
  "country": "RU",
  "loc": "59.9386,30.3141",
  "org": "AS44050 Petersburg Internet Network ltd.",
  "postal": "195213",
  "timezone": "Europe/Moscow"
}
```

composer require ipinfo/ipinfo

```shell
# With Basic Auth
$ curl -u <token>: ipinfo.io

# With Bearer token
$ curl -H "Authorization: Bearer <token>" ipinfo.io

# With token query parameter
$ curl ipinfo.io?token=<token>

$ curl ipinfo.io/json
$ curl ipinfo.io/8.8.8.8/json

$ curl -H "Accept: application/json" ipinfo.io
$ curl -H "Accept: application/json" ipinfo.io/8.8.8.8

ipinfo.io/[IP address]?token=<token>
```
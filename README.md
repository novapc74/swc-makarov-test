## SWC test (Makarov)
***
### 1. How to install
```shell
mkdir swc_test_makarov
cd swc_test_makarov
git clone git@github.com:novapc74/swc-makarov-test.git ./
make vendor
cp .env.example .env # установите свои переменные окружения
make key-gen
```

### 2. Seed database
```shell
make up
make migrate
make seed
```

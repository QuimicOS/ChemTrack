## Script TO Download
1) git clone to the repo
2) composer install
3) php artisan key:generate
4) php artisan config:clear
5) php artisan migrate

6) winget install Schniz.fnm

# configure fnm environment
fnm env --use-on-cd | Out-String | Invoke-Expression

# download and install Node.js
fnm use --install-if-missing 20

# verifies the right Node.js version is in the environment
node -v # should print `v20.18.0`

# verifies the right npm version is in the environment
npm -v 'should print last version'
7) npm install
8) npm run build
9) git status to see changes in files
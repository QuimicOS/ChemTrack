## Script to install the ChemTrack Project
1) git clone to the repo
2) composer install
    Create a ".env" file and paste the ".env copy" info into it. THIS IS FOR LOCAL USE ONLY. For deployment use the server info where is needed.
3) php artisan key:generate
4) php artisan config:clear
5) php artisan migrate

6) winget install Schniz.fnm
7) fnm env --use-on-cd | Out-String | Invoke-Expression
8) fnm use --install-if-missing 20
9) node -v # should print `v20.18.0`
10)npm -v 'should print last version'
11) npm install
12) npm run build
13) git status to see changes in files

module.exports = {
    'resources/{css,js}/**/*.{css,js}': ['prettier --write', 'git add'],
    '**/*': () => ['npm run build', 'git add resources/dist'],
    '**/*.php': ['./vendor/bin/pint'],
};

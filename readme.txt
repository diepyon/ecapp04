laravel8+vue2���C���X�g�[���ς݂̑f�ޔ̔��A�v��

php8
vue-router�C���X�g�[���ς�

�|�[�g�ԍ�8080
phpmyadmain��8081

�C���X�g�[���������f�B���N�g���Ɉړ��i�f�B���N�g�����ƃ_�E�����[�h�����̂ŐV���Ƀt�H���_�����K�v�͂Ȃ��j
git clone https://github.com/diepyon/Laravel8andVue2.git

�C�ӂ̖��̂ɕύX
stock newname

�f�B���N�g�����Ɉړ�����
docker compose up -d

�C���X�g�[����ɉ��L�̃R�}���h���K�v
docker compose exec app bash
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
chmod -R 777 storage bootstrap/cache
php artisan migrate
exit

githubn�̕R�Â��������K�v
git remote set-url origin {new-url}
git add -A
git commit -m "first commit"
git branch -M main
git push -u origin main
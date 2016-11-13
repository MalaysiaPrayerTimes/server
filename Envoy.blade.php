@setup
  $home = getenv('DEPLOY_DIR');
  $server = getenv('DEPLOY_SERVER');
@endsetup

@servers(['digitalocean' => $server])

@task('deploy')
  cd {{ $home }}
  git pull origin master
  composer install --prefer-dist
  php artisan route:cache
  php artisan config:cache
  php artisan migrate --force
@endtask

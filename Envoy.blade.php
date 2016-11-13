@setup
  function env(string $key) {
    $dotenv = file_get_contents('.env');
    $rows   = explode("\n", $dotenv);

    $search = array_filter($rows, function ($row) use ($key) {
      if (strstr($row, $key)) {
        return $row;
      }
    });

    $variable = reset($search);
    $segments = explode('=', $variable);
    $user = end($segments);

    return $user;
  }

  $home = env('DEPLOY_DIR');
  $server = env('DEPLOY_SERVER');
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

<?php

if (!isset($argv[1]))
{
  error_log("usage: {$argv[0]} <port>\n");
  exit(1);
}

$port = $argv[1];

require_once 'vendor/autoload.php';

$c = new \Lum\Socket\Client(['port'=>$port]);

$_client_listening = true;

while ($_client_listening)
{
  $line = trim(fgets(STDIN));
  if ($line != '')
  { // A command was passed.
    $c->write($line);
    $response = trim($c->read(1024));
    if ($response == 'GOODBYE' || $response == 'SHUTDOWN' || $response === false)
    {
      if ($response === false)
        $response = "CLOSED";
      echo "[$response]\n";
      $c->close();
      $_client_listening = false;
    }
    else
    {
      echo ">> $response\n";
    }
  }
  else
  { // Sleep for a bit.
    usleep(100);
  }
}
echo "Client exited\n";

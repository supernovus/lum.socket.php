<?php

if (!isset($argv[1]))
{
  error_log("usage: {$argv[0]} <port>\n");
  exit(1);
}

$port = $argv[1];

require_once 'vendor/autoload.php';

$s = new \Lum\Socket\Server(['port'=>$port]);
$_server_listening = true;

while ($_server_listening)
{ 
  $c = $s->accept();
  if ($c === false)
    usleep(100);
  elseif ($c->socket > 0)
  { 
    $_client_listening = true;
    do
    { 
      $command = trim($c->read(1024));
      switch ($command) {
        case 'QUIT':
          $c->write("SHUTDOWN\n");
          $c->close();
          $s->close();
          $_client_listening = false;
          $_server_listening = false;
          break;
        case 'END':
          $c->write("GOODBYE\n");
          $c->close();
          $_client_listening = false;
          break;
        default:
          $c->write("ECHO $command\n");
      }
    } 
    while ($_client_listening);
    echo "Client exited\n";
  }
  else
  { 
    error_log("error: ".$c->error_msg()."\n");
    exit(1);
  }
}
echo "Server shutdown\n";


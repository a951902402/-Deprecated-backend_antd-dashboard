<?php
session_start();
function test() {
  if(isset($_SESSION['name'])) {
    $_SESSION['name'] = 'meme';
  }
  else {
    $_SESSION['name'] = 'unset';
  }
  return session_id().' ** ** '.$_SESSION['name'];
}
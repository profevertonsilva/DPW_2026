<?php
// Certifique-se de que não houve saída (echo, espaços, HTML) antes deste código
header("Location: /App/View/dashboard.php"); //Envia para o dashboard para realização do teste do front-end, depois deve ser removido para direcionar para a página de login ou home, dependendo do fluxo de navegação definido.
exit(); 

// Guia para integração com o banco de dados e outras funcionalidades: https://docs.google.com/document/d/1ul-5mBNWv0_tzxtCLI2dK-7YQL3MyHh6CKHEx-lNLJI/edit?usp=sharing

?>
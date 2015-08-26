<html xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
<head><title>Magento to Vtex</title>
    <style>
        @import url(http://fonts.googleapis.com/css?family=Orbitron);
        @import url(http://fonts.googleapis.com/css?family=Roboto);
        h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 18px;
        }
        .formulario {
            font-family: Roboto, sans-serif;
            font-size: 13px;
        }
    </style>
</head>
<body>
<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>

<center>
    <div class="topo" style="height: 30px; width: 700px;">
        <p class="topolink" style="margin-left: 20px;">
            <a href="http://planilha.primordia.com.br/">Integrador correios</a> |
            <a href="http://planilha.primordia.com.br/sobeplanilhaindex.php">Sobe planilha</a> |
            <a href="http://magentotovtex.primordia.com.br/sobeplanilhaindex.php">Magento to Vtex</a>
        </p>
    </div>
    <table width=400><tr><td>
        <div class="formulario"><div class="conteudo">
            <h2 style="padding-top:20px;">Magento to Vtex</h2>
            <p>Preencha os campos abaixo com os dados necess&aacute;rios:</p>


            <form action="MagentoToVtex.php" enctype="multipart/form-data" id="integrador" method="post" name="integrador">

                <div class="magento">

                    <center>

                    <div class="nome_campo" >Dados Magento: </div>

                    <div class="nome_campo" >Nome cliente: </div>
                    <div class="campo"><input name="name" value="" maxlenght="60" type="text" class="forms"></div>

                    <div class="nome_campo" >URL: </div>
                    <div class="campo"><input name="url" value="" maxlenght="60" type="text" class="forms"></div>

                    <div class="nome_campo" >Usuario: </div>
                    <div class="campo"><input name="user" value="" maxlenght="60" type="text" class="forms"></div>

                    <div class="nome_campo" >Password: </div>
                    <div class="campo"><input name="pass" value="" maxlenght="60" type="password" class="forms"></div>

                    <div class="nome_campo" >DB host: </div>
                    <div class="campo"><input name="dbhost" value="" maxlenght="60" type="text" class="forms"></div>

                    <div class="nome_campo" >Usuario DB: </div>
                    <div class="campo"><input name="userdb" value="" maxlenght="60" type="text" class="forms"></div>

                    <div class="nome_campo" >Password DB: </div>
                    <div class="campo"><input name="passdb" value="" maxlenght="60" type="password" class="forms"></div>

                    </center>
                    <p></p>
                </div>
<p></p>
                 <div class="vtex">

                    <center>

                    <div class="nome_campo" >Dados Vtex: </div>

                    <div class="nome_campo" >Nome da loja na vtex(account name): </div>
                    <div class="campo"><input name="accountname" value="" maxlenght="60" type="text" class="forms"></div>

                    <div class="nome_campo" >Usuário vtex: </div>
                    <div class="campo"><input name="vtexuser" value="" maxlenght="60" type="text" class="forms"></div>

                    <div class="nome_campo" >Senha vtex: </div>
                    <div class="campo"><input name="vtexpass" value="" maxlenght="60" type="password" class="forms"></div>

                    </center>
                     <p></p>
                </div>
                <p>
                    <div class="botao"><input class="botao-enviar" title="Send" type="submit" value="Integrar"></div>
                </p>

            </form>
        </div></div>
    </td></tr></table></center>
<style>
    .magento {border: 2px solid #8AC007;border-radius: 5px;width: 345px;}
    .vtex {border: 2px solid #8AC007;border-radius: 5px;width: 345px;}

    .nome_campo{padding-top: 10px;   border-radius: 5px;}
    body {background-color:lightblue;}
    .formulario {background-color:white;border-radius:10px;}
    .conteudo {padding-left:20px;padding-bottom:20px;}
</style>
</body>
</html>
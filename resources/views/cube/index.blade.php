<!DOCTYPE html>
<html>
<head>
    <title>Cube Summation.</title>
    <!-- El Meta Viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- El CSS de Material Design con un color establecido , yo usaré el color Rosado Índigo -->
    <link rel="stylesheet" href="mdi/material.min.css">
    <!-- El archivo JS de Material Design -->
    <script src="mdi/material.min.js"></script>
    <script src="libs/jquery-3.1.1.min.js"></script>
    <script src="libs/jquery-ui.js"></script>
    <script src="js/app.js"></script>

    <!-- Un tipo de Fuente desde Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>
<body>
<style>
    #info {
        background: rgba(0, 255, 243, 0.05);
        padding: 20px;
    }
</style>
<div class="container">
    <div id="p2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate hidden"></div>
    <!-- Simple header with fixed tabs. -->
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header
						mdl-layout--fixed-tabs">
        <header class="mdl-layout__header">
            <div class="mdl-layout__header-row">
                <!-- Title -->
                <span class="mdl-layout-title"><img src="img/logo.png" width="150"></span>
            </div>
        </header>
        <main class="mdl-layout__content">
            <div class="mdl-grid mdl-cell--4-offset">
                <div class="mdl-cell mdl-cell--5-col">
                    <div id="info">
                        <strong>Restricciones</strong><br>
                        1 <= t <= 50<br>
                        1 <= N <= 100 <br>
                        1 <= M <= 1000 <br>
                        1 <= x1 <= x2 <= N <br>
                        1 <= y1 <= y2 <= N <br>
                        1 <= z1 <= z2 < N <br>
                        1 <= x, y, z <= N <br>
                        -10 9 <= W <= 10 9 <br>
                    </div>
                </div>
            </div>
            <form action="cube/process" method="post" name="formCube">
                <div class="mdl-grid mdl-cell--4-offset">
                    <div class="mdl-cell mdl-cell--6-col ">
                        <div class="mdl-textfield mdl-js-textfield">
                            {{ csrf_field() }}
                            <textarea class="mdl-textfield__input" type="text" rows="10" name="data"
                                      id="sample5"></textarea>
                            <label class="mdl-textfield__label" for="sample5">Ingrese aqui la data!</label>
                        </div>
                    </div>
                    <div class="mdl-cell mdl-cell--12-col">
                        <button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored">
                            Procesar
                        </button>
                    </div>
                </div>
            </form>
        </main>
        <footer class="mdl-mini-footer">
            <div class="mdl-mini-footer__left-section">
                <div class="mdl-logo">By Haderon Bullón</div>
                <ul class="mdl-mini-footer__link-list">
                    <li>Cube Summation</li>
                </ul>
            </div>
        </footer>
    </div>
</div>
</body>
</html>


<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Http\Requests;
use App\Http\Controllers\Controller;
use Mockery\CountValidator\Exception;

class CubeController extends Controller
{
    private static $matrix = '';

    //Número de casos
    private $t;

    //El texto ingresado convertido en array, sin el primer elemento
    private $data;

    //Contador de Casos
    private $countT = 1;

    //Bandera para saber si toda la data es válida
    private $flag = true;

    //Contador de operaciones
    private $countO;

    //Contar los bloques de contenidos
    private $bloque;

    //Arreglo para guardar los datos n y m
    private $arrnm = [];

    //Guarda las matrices que se generando
    private $matriz = [];

    //Indica en que posición empezará a recorrer el array con las operaciones.
    private $start = 2;

    //Almacena los resultados
    private $results = [];

    //Almacena los errors
    private $errors = [];

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('cube.index');
    }

    /**
     * Process data
     *
     * @return \Illuminate\Http\Response
     */
    public function process(Request $request)
    {

        $exp = explode("\n", $request['data']);

        //Obtengo el valor de "T"
        $this->t = $exp[0];

        /*Elimina la primera posición del array
        para facilitar el parseo de la data*/
        unset($exp[0]);

        $this->data = $exp;

        if ($this->t == "" || strpos($this->t, ' '))
        {

            $this->flag = false;
            $msg = 'El valor de "T" no fue enviado!';
            array_push($this->errors, $msg);

            return response()->json(['errors' => $this->errors], 400);

        }

        $this->validateText();

        if (!$this->flag)
        {
            return response()->json(['errors' => $this->errors], 400);
        //Entonces debo construir la matriz y realizar los queries
        } else {

            for ($x = 0; $x < $this->t; $x++)
            {

                //Creo la matríz número x
                $this->getMatriz($x);
                $this->execOperation($x);

            }

            return response()->json(["results" => $this->results], 200);

        }

    }

    /**
     * @text string
     * Assigns the values of N y M in $this->arrnm[]
     */
    private function setNM($text)
    {
        //Obtengo la posición del espacio en blanco según el formato establecido
        $post = strpos($text, ' ');
        //Guardo los valores de n y m
        array_push($this->arrnm, [substr($text, 0, $post), substr($text, $post)]);

    }

    /**
     * Use @this->data
     * Validate data
     */
    private function validateText()
    {

        for ($x = 1; $x <= count($this->data); $x++)
        {
            $this->queryValidate($this->data[$x]);
        }

    }

    /**
     * @query String
     * Determine type of text and validate its format text
     */
    private function queryValidate($query)
    {
        //Verificar si se trata de n y m
        if (strlen($query) <= 9)
        {

            //Seteo los valores de n y m
            $this->setNM($query);
            //Inicializo el coador de operaciones
            $this->countO = 1;
            //Voy contando los bloques de contenidont
            $this->bloque++;

            //Si el contador de casos (countT) <= $this->t
            if ($this->countT <= $this->t)
            {
                $this->countT++;
            } else {

                $this->flag = false;
                $msg = 'El número de casos no puede ser > "T": ' . $this->t . ' tenga cuidado con los espacios en blanco';
                array_push($this->errors, $msg);

            }

        //Si se trata de un query normal, verifica el formato del mismo
        } else {

            //Convierto la cadena en un array
            $cadena = explode(' ', $query);

            //Guardo un error si el contador de operaciones es > m
            if ($this->countO > $this->getM())

            {
                $this->flag = false;
                $msg = 'Número de operaciones excedida, el límite es:' . $this->getM() . ' en el bloque:' . $this->bloque;
                array_push($this->errors, $msg);

            }

            $this->countO++;

            //Es un "UPDATE"
            if ($cadena[0] == 'UPDATE')
            {

                /*Al convertir esto UPDATE 2 2 2 4 en un array
                No pueden haber más de 5 posiciones*/
                if (count($cadena) > 5)
                {

                    $this->flag = false;
                    $msg = 'Tiene algunos espacios en blanco demás en:' . $query;
                    array_push($this->errors, $msg);

                } else {
                    $this->validateBlock($cadena, 1);
                }

            } else {

                //Es un "QUERY/"
                if ($cadena[0] == 'QUERY')
                {

                    //Al convertir esto QUERY 1 1 1 3 3 3 en un array
                    //No pueden haber más de 7 posiciones
                    if (count($cadena) > 7)
                    {

                        $this->flag = false;
                        $msg = 'Tiene algunos espacios en blanco demás en:' . $query;
                        array_push($this->errors, $msg);

                    } else {
                        $this->validateBlock($cadena, 2);
                    }

                } else {

                    $this->flag = false;
                    $msg = 'Palabra inválida en la operación: ' . $cadena[0];
                    array_push($this->errors, $msg);

                }
            }

        }
    }

    /*
    *@block array operation data
    *@type integer 1 => UPDATE & 2 QUERY
    */
    private function validateBlock($block, $type)
    {

        //Variables locales para almacenar coordenadas
        $x1 = "";
        $x1 = "";
        $y1 = "";
        $z1 = "";
        $x2 = "";
        $y2 = "";
        $z2 = "";

        /*Elimino el primer elemento del array (UPDATE/QUERY)
        Para facilitar lectura;*/
        unset($block[0]);

        //UPDATE
        if ($type == 1)
        {
            $x1 = $block[1];
            $y1 = $block[2];
            $z1 = $block[3];

            if ( count($block) !== 4)
            {

                $this->flag = false;
                $msg = 'Este bloque debería tener 4 elementos: ' . implode(' ', $block);
                array_push($this->errors, $msg);

            }

            if ($x1 !== $y1 || $x1 !== $z1)
            {

                $this->flag = false;
                $msg = 'x1,y1,z1 no pueden ser diferentes ' . implode(' ', $block);
                array_push($this->errors, $msg);

            }

            if ($x1 > $this->getN() || $y1 > $this->getN()|| $z1 >$this->getN())
            {

                $this->flag = false;
                $msg = 'x1,y1,z1 no pueden mayor que: ' . $this->getN();
                array_push($this->errors, $msg);

            }

        //QUERY
        } else {

            //Asigno cada elemento  en su respectiva coordenada
            if (count($block) == 6)
            {

                $x1 = $block[1];
                $y1 = $block[2];
                $z1 = $block[3];
                $x2 = $block[4];
                $y2 = $block[5];
                $z2 = $block[6];


                //Se cumple esta condicion: 1 <= x1 <= x2 <= N
                if ($x1 < 1 || $x1 > $x2 || $x2 > $this->getN())
                {

                    $this->flag = false;
                    $msg = 'Se está violando la siguiente restricción: 1 <= x1 <= x2 <= N';
                    array_push($this->errors, $msg);

                }
                //Se cumple esta condicion: 1 <= y1 <= y2 <= N
                if ($y1 < 1 || $y1 > $y2 || $y2 > $this->getN())
                {

                    $this->flag = false;
                    $msg = 'Se está violando la siguiente restricción: 1 <= y1 <= y2 <= N';
                    array_push($this->errors, $msg);

                }

                //Se cumple esta condicion: 1 <= z1 <= z2 <= N
                if ($z1 < 1 || $z1 > $z2 || trim($z2) > $this->getN())
                {

                    $this->flag = false;
                    $msg = 'Se está violando la siguiente restricción: 1 <= z1 <= z2 <= N';
                    array_push($this->errors, $msg);

                }

                if ($x1 !== $y1 && $y1 !== $z1)
                {

                    $this->flag = false;
                    $msg = 'x1,y1,z1 no pueden ser diferentes ' . implode(' ', $block);
                    array_push($this->errors, $msg);

                }

                if ($x2 !== $y2 && $y2 !== $z2)
                {

                    $this->flag = false;
                    $msg = 'x2,y2,z2 no pueden ser diferentes ' . implode(' ', $block);
                    array_push($this->errors, $msg);

                }

                if ($x1 !== $y1 && $x1 !== $z1)
                {

                    $this->flag = false;
                    $msg = 'x1,y1,z1 no pueden ser diferentes ' . implode(' ', $block);
                    array_push($this->errors, $msg);

                }

                if ($x2 !== $y2 && $x2 !== trim($z2))
                {

                    $this->flag = false;
                    $msg = 'x2,y2,z2 no pueden ser diferentes ' . implode(' ', $block);
                    array_push($this->errors, $msg);

                }

            } else {

                $this->flag = false;
                $msg = 'Este bloque debería tener 6 elementos: ' . implode(' ', $block);
                array_push($this->errors, $msg);

            }

        }

    }

    /*
     *Return value of "N"
     *@return integer
    */
    private function getN()
    {
        if(count($this->arrnm) > 0)
        {
            return $this->arrnm[count($this->arrnm) - 1][0];
        }
    }

    /*
     *Return value of "M"
     *@return integer
    */
    private function getM()
    {
        if(count($this->arrnm) > 0)
        {
            return $this->arrnm[count($this->arrnm) - 1][1];
        }
    }

    /*
     *Get matriz
     *@numero integer
    */
    public function getMatriz($numero)
    {

        $this->matriz = [];

        //Genera la matríz con la cantidad de filas que indica $this->arrnm[$numero][0]
        for ($i = 0; $i < $this->arrnm[$numero][0]; $i++)
        {
            array_push($this->matriz, [0, 0, 0]);
        }

    }

    /*
    *Exec operation
    *@numero integer
   */
    public function execOperation($numero)
    {
        /*Recorro todas las operaciones a ejcutar sobre la matríz en cuestión.
        Inicia el ciclo en el valor que tenga strart hasta el valor de M+*/
        for ($x = $this->start; $x < ($this->start + $this->arrnm[$numero][1]); $x++)
        {

            $row = explode(" ", $this->data[$x]);

            if ($row[0] == "UPDATE")
            {

                $this->matriz[$row[1] - 1][0] = $row[4];
                $this->matriz[$row[1] - 1][1] = $row[4];
                $this->matriz[$row[1] - 1][2] = $row[4];

            }
            if ($row[0] == "QUERY")
            {

                $total = 0;
                $acum = 0;
                for ($i = $row[1]; $i <= $row[6]; $i++)
                {
                    $acum = $acum + $this->matriz[$i - 1][0];
                }
                array_push($this->results, $acum);

            }

        }

        /*Asigno el núevo valor de start
        De esta manera el próximo ciclo empieza desde otra posición*/
        $this->start = $this->start + $this->arrnm[$numero][1] + 1;

    }

}

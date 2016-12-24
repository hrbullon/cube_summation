<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CubeController extends Controller
{
    private static $matrix = '';
	
	
	private $t; //Número de casos
	private $data; //El texto ingresado convertido en array, sin el primer elemento
	private $flagT; //Bandera para saber si se excede el límite de casos
	private $countT = 1; //Contador de Casos
	private $flag = true; //Bandera para saber si toda la data es válida
	private $msg = 'Invalid data <br>'; //Variable para guardar los mensajes de error. 
	private $countO; //Contador de operaciones
	private $bloque;
	private $arrnm = []; //Arreglo para guardar los datos n y m
	private $matriz = [];
	private $start = 2; //Indica en que posición empezará a recorrer el array con las operaciones.
	
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cube.create');
    }
	
	public function process(Request $request)
	{
		$exp = explode("\n", $request['data']);
		$lineas = count($exp);
		//Obtengo el valor de "T"
		$this->t = $exp[0];
		//Elimina la primera posición del array
		//para facilitar el parseo de la data
		unset($exp[0]);
		
		$this->data = $exp;
		
		echo 'Input data<br>';
		echo '<pre>'.$request['data'].'</pre>';
		
		$this->validateText();
		
	}
	
	
	private function setNM($text)
	{
		//Obtengo la posición del espacio en blanco según el formato establecido
		$post =  strpos($text,' ');
		//Guardo los valores de n y m	
		array_push($this->arrnm,[substr($text, 0, $post), substr($text, $post)]);
		
	}
	
	private function validateText()
	{
	
		
		for($x = 1; $x <= count($this->data); $x++)
		{
			
			$this->queryValidate($this->data[$x]);
			
		}
		
		if(!$this->flag)
		{
			echo $this->msg;
		//Entonces debo construir la matriz y realizar los queries
		}else{
			
			echo 'Output data<br>';

			for($x = 0; $x < $this->t; $x++)
			{
				//Creo la matríz número x
				$this->getMatriz($x);
				$this->execOperation($x);
			}
			
		}
		
	} 
	
	private function queryValidate($query)
	{
		//Verificar si se trata de n y m
		if(strlen($query) <= 9)
		{
			
			$this->setNM($query);
			$this->countO = 1;
			$this->bloque++;
			
			if($this->countT <= $this->t)
			{
				$this->countT++;
			}else{
				$this->flag = false;
				$this->msg .= ' The number of test-cases can\'t be > "T": ' . $this->t . ' becareful with blank spaces<br>';	
			}
		//Si se trata de un query normal, verifica el formato del mismo
		}else{
			//Convierto la cadena en un array
			$cadena = explode(' ',$query);
			
			if($this->countO > $this->getM())
			{
				$this->flag = false;
				$this->msg .= 'Número de operaciones excedida, el límite es:'.$this->getM().' en el bloque:'.$this->bloque.'<br>';	

			}
			
			$this->countO++;
			
			//Es un "UPDATE"
			if($cadena[0] == 'UPDATE')
			{
				if(count($cadena) > 5)
				{
					$this->flag = false;
					$this->msg .= 'Tiene algunos espacios en blanco demás en:<pre>'.$query.'</pre>';	
				}else{
					$this->validateBlock($cadena,1);
				}
							
			}else{
				//Es un "QUERY/"
				if($cadena[0] == 'QUERY')
				{
					if(count($cadena) > 7)
					{
						$this->flag = false;
						$this->msg .= 'Tiene algunos espacios en blanco demás en:<pre>'.$query.'</pre>';	
					}else{
						$this->validateBlock($cadena,2);
					}
					
				}else{
					$this->flag = false;
					$this->msg .= 'Invalid word in operation: '. $cadena[0].'<br>';
				}	
			}
			
			
		}				
	}
	
	/*
	*@block array datos de la operación
	*@type int = 1/2
	*/
	private function validateBlock($block,$type)
	{
		//Variables locales para almacenar coordenadas
		$x1;$y1;$z1;
		$x2;$y2;$z2;
		
		//Elimino el primer elemento del array (UPDATE/QUERY)
		unset($block[0]);
		//UPDATE		
		if($type == 1)
		{
			if(count($block) == 4)
			{
				
			}else{
				$this->flag = false;
				$this->msg .= 'Este bloque debería tener 4 elementos: '. implode(' ',$block);
			}
		//QUERY	
		}else{
			if(count($block) == 6)
			{
				
				$x1 = $block[1];
				$y1 = $block[2];
				$z1 = $block[3];
				$x2 = $block[4];
				$y2 = $block[5];
				$z2 = $block[6];

		
				//Se cumple esta condicion: 1 <= x1 <= x2 <= N 
				if( $x1 < 1 || $x1 > $x2 || $x2 > $this->getN())
				{
					$this->flag = false;
					$this->msg .= 'Se está violando la siguiente restricción: 1 <= x1 <= x2 <= N <br>';
				}
				//Se cumple esta condicion: 1 <= y1 <= y2 <= N 
				if( $y1 < 1 || $y1 > $y2 || $y2 > $this->getN())
				{
					$this->flag = false;
					$this->msg .= 'Se está violando la siguiente restricción: 1 <= y1 <= y2 <= N <br>';
				
				}
				//Se cumple esta condicion: 1 <= z1 <= z2 <= N 
				if( $z1 < 1 || $z1 > $z2 || trim($z2) > $this->getN())
				{
					$this->flag = false;
					$this->msg .= 'Se está violando la siguiente restricción: 1 <= z1 <= z2 <= N <br>';

				}
				
				if( $x1 !== $y1 && $y1 !== $z1)
				{
					$this->flag = false;
					$this->msg .= 'x1,y1,z1 no pueden ser diferentes '.implode(' ',$block).'<br>';
				
				}
				
				if( $x2 !== $y2 && $y2 !== $z2)
				{
					$this->flag = false;
					$this->msg .= 'x2,y2,z2 no pueden ser diferentes '.implode(' ',$block).'<br>';
				}
		
			}else{
				$this->flag = false;
				$this->msg .= 'Este bloque debería tener 6 elementos: '. implode(' ',$block);
			}
		}
	}
	
	
	private function getN()
	{
		return $this->arrnm[count($this->arrnm)-1][0];
	}
	
	private function getM()
	{
		return $this->arrnm[count($this->arrnm)-1][1];		
	}
	
	public function getMatriz($numero)
	{
		$this->matriz = [];		
		for($i = 0; $i < $this->arrnm[$numero][0]; $i++)
		{	
			array_push($this->matriz, [0,0,0]);
		}
			
	}	
	
	public function execOperation($numero)
	{
		//Recorro todas las operaciones a ejcutar sobre la matríz en cuestión.
		for($x = $this->start; $x < ($this->start+$this->arrnm[$numero][1]); $x++)
		{
			$row = explode(" ",$this->data[$x]);
			if($row[0] == "UPDATE")
			{
				$this->matriz[$row[1]-1][0] = $row[4];
				$this->matriz[$row[1]-1][1] = $row[4];
				$this->matriz[$row[1]-1][2] = $row[4];
				
			}
			if($row[0] == "QUERY")
			{
				$total = 0;
				$acum = 0;
				for($i = $row[1]; $i <= $row[6]; $i++)
				{
					$acum = $acum + $this->matriz[$i-1][0];
				}
				echo $acum ."<br>";
			}
		}
		
		$this->start = $this->start+$this->arrnm[$numero][1]+1;//Esto hace que inicie 2 filas más adelante.
		
	}

}

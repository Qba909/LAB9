<?php
namespace app\controllers;

use app\forms\CalcForm;
use app\transfer\CalcResult;

class CalcCtrl {

	private $form;   //dane formularza (do obliczeń i dla widoku)
	private $result; //inne dane dla widoku
	private $hide_intro; //zmienna informująca o tym czy schować intro

	public function __construct(){
		//stworzenie potrzebnych obiektów
		$this->form = new CalcForm();
		$this->result = new CalcResult();
		$this->hide_intro = false;
	}
	
	/** 
	 * Pobranie parametrów
	 */
	public function getParams(){
                $this->form->x = getFromRequest('x');
                $this->form->y = getFromRequest('y');
                $this->form->z = getFromRequest('z');
	}
	
	/** 
	 * Walidacja parametrów
	 * @return true jeśli brak błedów, false w przeciwnym wypadku 
	 */
        
        public function validate(){
            // sprawdzenie, czy parametry zostały przekazane
            if ( ! (isset($this->form->x) && isset($this->form->y) && isset($this->form->z) )) return false;
   
            $this->hide_intro = true;

            // sprawdzenie, czy potrzebne wartości zostały przekazane
            if ($this->form->x == "") {
			getMessages()->addError('Nie podano kwoty');
		}
            if ($this->form->y == "") {
			getMessages()->addError('Nie podano oprocentowania');
		}
            if ($this->form->z == "") {
			getMessages()->addError('Nie podano liczby rat');
		}
                
            if (! getMessages()->isError()) {
			
			// sprawdzenie, czy $x i $y są liczbami całkowitymi
			if (! is_numeric ( $this->form->x )) {
				getMessages()->addError('Pierwsza wartość nie jest liczbą');
			}
			if (! is_numeric ( $this->form->y )) {
				getMessages()->addError('Druga wartość nie jest liczbą');
			}
                        if (! is_numeric ( $this->form->z )) {
				getMessages()->addError('Trzecia wartość nie jest liczbą');
			}
		}
		
		return ! getMessages()->isError();
              
        }

	/** 
	 * Pobranie wartości, walidacja, obliczenie i wyświetlenie
	 */
	public function action_calcCompute(){

		$this->getparams();
                
		if ($this->validate()) {
			
                       getMessages()->addInfo('Parametry poprawne. Wykonuję obliczenia.');

                        //konwersja parametrów na int
                        $this->form->x = floatval($this->form->x);
                        $this->form->y = floatval($this->form->y);
                        $this->form->z = floatval($this->form->z);
                        
				
			//wykonanie operacji
                        $this->result->result = ($this->form->x + ($this->form->x * $this->form->y / 100)) / $this->form->z;
			
			getMessages()->addInfo('Wykonano obliczenia.');
		}
		
		$this->generateView();
	}
	
	
	public function action_calcShow(){
		getMessages()->addInfo('Witaj w kalkulatorze');
		$this->generateView();
	}
        
	public function generateView(){
			
                
                getSmarty()->assign('user',unserialize($_SESSION['user']));
                
		getSmarty()->assign('page_title','Strona główna');
		getSmarty()->assign('page_description','Routing');
		getSmarty()->assign('page_header','Routing');
				
		getSmarty()->assign('hide_intro',$this->hide_intro);
                
		getSmarty()->assign('form',$this->form);
		getSmarty()->assign('res',$this->result);
		
		getSmarty()->display('calc_view.tpl');
	}
}
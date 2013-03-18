<?php

class Patient
{
	private $namePatient;
	private $listExercises;

	public function getNamePatient(){
		return $this->namePatient;
	}

	public function setNamePatient($name){
		$this->namePatient = $name;
	}

	public function getListExercises(){
		return $this->listExercises;
	}

	public function __construct($name){
		$this->namePatient = $name;
		$this->listExercises = array();
	}

	public function addExercise($exercise){
		$this->listExercise[]= $exercise;
	}

	

	

}


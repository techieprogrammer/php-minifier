<?php
class PHPMinifier {
	
	private $fileName;
	
	public function __construct() {
		$this->fileName = "";
	}
	
	public function apply($fileLocation = "") {	
		if($fileLocation == "")
		{ return; }
		else
		{
			if(file_exists($fileLocation)) {
				$temp = explode("/",$fileLocation);
				$this->fileName = trim(end($temp));
				$fileContent = trim(file_get_contents($fileLocation, true));
				if($fileContent == "")
				{	return;	}
			}
			else
			{	return; }
		
			$minifierString = "";
			$lastCharacter = "";

			$counter = 0;
			$contentSize = strlen($fileContent);					
			while($counter < $contentSize) {
				$character = $fileContent[$counter];
				$code = intval(ord($character));
				if($code == 10) //Find EOL (End of Line)
				{	$minifierString .= " ";	}
				else if($character == "\t") //Find Tab
				{	$minifierString .= "";	}
				else if($character == "#") { //Find Single Line (#.....) Comment
					$counter++;
					while($counter < $contentSize) {
						$character = $fileContent[$counter];
						$code = intval(ord($character));
						if($code == 10) { //Find EOL (End of Line)
							$minifierString .= " ";
							break;
						}
						$counter++;
					}
				}
				else if($character == "\"" || $character == "'") { //Find Double or Single Quote
					$minifierString .= $character;
					$foundCharacter = $character;
					$counter++;
					while($counter < $contentSize) {
						$character = $fileContent[$counter];
						if($character == $foundCharacter) {
							$minifierString .= $character;
							if($lastCharacter == "\\") {
								$lastCharacter = "";
							}
							else
							{	break;	}
						}
						else if($character == "\\" && $lastCharacter == "\\") {
							$minifierString .= $character;
							$lastCharacter = "";
						}
						else
						{
							$lastCharacter = $character;
							$code = intval(ord($character));
							if($code != 10) 
							{	$minifierString .= $character;	}
							else
							{	$minifierString .= " ";	}
						}
						$counter++;
					}
				}
				else if($character == "/" && (isset($fileContent[$counter+1]) && ($fileContent[$counter+1] == "*" || $fileContent[$counter+1] == "/"))) { //  Single (//....) and Multiple Lines (/*  */)  Comment
					if($fileContent[$counter+1] == "*") {
						$counter += 2;
						$checkCharacter = "*";
						while($counter < $contentSize) {
							if($fileContent[$counter] == $checkCharacter) {
								if($checkCharacter == "*") 
								{	$checkCharacter = "/";	}
								else 
								{
									$minifierString .= " ";
									break;
								}
							}
							$counter++;
						}
					}
					else
					{
						$counter += 2;
						while($counter < $contentSize) {
							$character = $fileContent[$counter];
							$code = intval(ord($character));
							if($code == 10) {
								$minifierString .= " ";
								break;
							}
							$counter++;
						}
					}
				}
				else
				{	$minifierString .= $character;	}
				$counter++;
			}
			echo $minifierString;

			//Please remove the below comment, if you want to store the compress minifier string in file (compress-minifier.txt), please check this file permission (read/write) also.
			/*
			$outputFile = "./compress-minifier.txt";
			$fileObj = fopen($outputFile, "w+") or die("Unable to create/open file!");
			fwrite($fileObj, $minifierString);
			fclose($fileObj);
			if(file_exists($outputFile)) 
			{	echo "Please find the minifier script in <b>[".$outputFile."]</b>";	}
			else
			{	echo "Couldn't able to produce the output";	}
			*/
		}
	}
}
?>

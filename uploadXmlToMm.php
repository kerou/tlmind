<?php
	$l__type                 = $_POST['type'];
	$l__prefix               = $_POST['prefix'];
	$l__directory            = 'upload/';
	$l__filename             = basename($_FILES['xmlf']['name']);
	$l__max_size             = 1000000;
	$l__file_size            = filesize($_FILES['xmlf']['tmp_name']);
	$l__available_extensions = array('.xml');
	$l__file_extension       = strrchr($_FILES['xmlf']['name'], '.'); 

	$l__return_form_string = '<br/><a href="index.php">Return to the form</a>';
	if($l__type=='requirements')
	{
		echo '***Error: "requirements" option is not yet supported.'.$l__return_form_string;
		exit;
	}
	
	if(!in_array($l__file_extension, $l__available_extensions)) //Si l'extension n'est pas dans le tableau
	{
		 echo '***Error: You must upload a .xml file...'.$l__return_form_string;
		 exit;
	}
	if($l__file_size>$l__max_size)
	{
		echo '***Error: The file is too big'.$l__return_form_string;
		 exit;
	}
	//filename format
	$l__filename_ori = $l__filename;
	$l__filename = strtr($l__filename, 
		  '¿¡¬√ƒ≈«»… ÀÃÕŒœ“”‘’÷Ÿ⁄€‹›‡·‚„‰ÂÁËÈÍÎÏÌÓÔÚÛÙıˆ˘˙˚¸˝ˇ', 
		  'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
	$l__filename = preg_replace('/([^.a-z0-9]+)/i', '-', $l__filename);
	if(!move_uploaded_file($_FILES['xmlf']['tmp_name'], $l__directory . $l__filename)) //Si la fonction renvoie TRUE, c'est que Áa a fonctionnÈ...
	{
		print_r($_FILES);
		echo '<br/> -- '.$_FILES['xmlf']['name'].' -- Error !'.$l__return_form_string;
		exit;
	}
	
	// --- write the header of the CSV file (txt file in fact)----------------------------------------
	header("Expires: 0");
	header("Cache-Control: private");
	header("Pragma: cache");
	header("Content-type: xml/xml");
	header("Content-Disposition: attachment; filename=xml-".time().".mm"); // name of the uploaded file
	// --- end of header------------------------------------------------------------------------------
	
	echo '<map version="0.9.0">
<!-- To view this file, download free mind mapping software FreeMind from http://freemind.sourceforge.net -->';
	echo "\n";
	
	$l__buffer = '';
	
	$l__complete_file_name = $l__directory . $l__filename;
	
	$l__ext_id = 1;
	$l__tc_id  = 1;
	$l__ts_id  = 1;
	
	// ------------------------------------------------------------------------------------------
	function printTestLinkXML($fileName) {
		global $l__filename_ori;
		$dom = new DomDocument();
		$dom->load($fileName);
		$root = $dom->documentElement;
		getElement($root,0,1,$l__filename_ori);
	}
	// ------------------------------------------------------------------------------------------
	
	// ------------------------------------------------------------------------------------------
	function get_step_values($i__node){
		$o__values = array();
		$l__child_nodes = $i__node->childNodes;
		foreach($l__child_nodes as $l__child_node)
		{
			if ($l__child_node->nodeType == XML_ELEMENT_NODE and in_array($l__child_node->nodeName,array('actions','expectedresults'))) {
				$l__textcontent = str_replace("\"", "''", strval($l__child_node->textContent));
				$l__textcontent = str_replace("</p>", "", $l__textcontent);
				$l__textcontent = str_replace("<p>", "", $l__textcontent);
				if(mb_detect_encoding($l__textcontent, "auto", true)=="UTF-8"){
					$l__textcontent = utf8_decode($l__textcontent);
				}
				$o__values[$l__child_node->nodeName] = $l__textcontent;
			}
		}
		return $o__values;
	}
	// ------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------
	 function getElement($dom_element,$i__level,$i__node_order,$i__force_name='') {
		
		global $l__tc_id;
		global $l__ts_id;
		global $l__ext_id;
		global $l__prefix;
		$l__indentation = '';
		for($i=1;$i<=$i__level;$i++)
		{
			$l__indentation .= '	';
		}
		// rÈcupÈration du nom de l'ÈlÈment
		$l__node_name = $dom_element->nodeName;

		// rÈcupÈration de la valeur CDATA, 
		// en supprimant les espaces de formatage.
		$l__textValue = trim($dom_element->firstChild->nodeValue);
		// RÈcupÈration de l'attribut TEXT
		$l__name = '';
		if(in_array($dom_element->nodeName,array('testsuite','testcase','step'))){
			$l__name =  $dom_element->getAttribute("name");
			$l__name = str_replace("\"", "''", $l__name);
			if(mb_detect_encoding($l__name, "auto", true)=="UTF-8"){
				$l__name = utf8_decode($l__name);
			}
		}
		if($dom_element->nodeName=='testsuite'){
			// TEST SUITE
			$l__name = str_replace("$l__prefix-TS-", "", $l__name); // remove the prefix
			if($l__name==''){
				$l__name = $i__force_name;
			}
			echo "$l__indentation<node TEXT=\"$l__name\">\n";
			echo "$l__indentation<icon BUILTIN=\"stop\"/>\n";
		}
		elseif($dom_element->nodeName=='testcase'){
			// TEST CASE
			echo "$l__indentation<node TEXT=\"$l__name\">\n";
			echo "$l__indentation<icon BUILTIN=\"stop\"/>\n";
		}
		elseif($dom_element->nodeName=='step'){
			// TEST STEP
			$l__step_values = get_step_values($dom_element);
			
			if(isset($l__step_values['actions']) and $l__step_values['actions']!=''){
				echo "$l__indentation<node TEXT=\"{$l__step_values['actions']}\">\n";
				echo "$l__indentation	<icon BUILTIN=\"idea\"/>\n";
				if(isset($l__step_values['expectedresults']) and $l__step_values['expectedresults']!=''){
					echo "$l__indentation	<node TEXT=\"{$l__step_values['expectedresults']}\"/>\n";
				}
			}
			else
			{
				echo "$l__indentation<node TEXT=\"unknown step\">\n";
			}
		}
		if ($dom_element->childNodes->length > 1) {
			$l__node_order = ($i__level==0) ? 1 : 100;
		  foreach($dom_element->childNodes as $dom_child) {
			if ($dom_child->nodeType == XML_ELEMENT_NODE) {
			  $l__node_order = getElement($dom_child,$i__level+1,$l__node_order);
			}
		  }
		}
		if($dom_element->nodeName=='testsuite'){
			echo "$l__indentation</node>\n";
		}
		elseif($dom_element->nodeName=='testcase'){
			echo "$l__indentation</node>\n";
		}
		elseif($dom_element->nodeName=='step'){
			echo "$l__indentation</node>\n";
		}
		return $i__node_order;
	}
	// ------------------------------------------------------------------------------------------

	printTestLinkXML($l__complete_file_name);
	echo '</map>';
?>

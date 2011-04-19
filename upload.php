<?php
	$l__type                 = $_POST['type'];
	$l__directory            = 'upload/';
	$l__filename             = basename($_FILES['mm']['name']);
	$l__max_size             = 100000;
	$l__file_size            = filesize($_FILES['mm']['tmp_name']);
	$l__available_extensions = array('.mm');
	$l__file_extension       = strrchr($_FILES['mm']['name'], '.'); 

	$l__return_form_string = '<br/><a href="index.php">Return to the form</a>';
	if($l__type=='requirements')
	{
		echo '***Error: "requirements" option is not yet supported.'.$l__return_form_string;
		exit;
	}
	
	if(!in_array($l__file_extension, $l__available_extensions)) //Si l'extension n'est pas dans le tableau
	{
		 echo '***Error: You must upload a .mm file...'.$l__return_form_string;
		 exit;
	}
	if($l__file_size>$l__max_size)
	{
		echo '***Error: The file is too big'.$l__return_form_string;
		 exit;
	}
	//filename format
	$l__filename = strtr($l__filename, 
		  'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜİàáâãäåçèéêëìíîïğòóôõöùúûüıÿ', 
		  'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
	$l__filename = preg_replace('/([^.a-z0-9]+)/i', '-', $l__filename);
	if(!move_uploaded_file($_FILES['mm']['tmp_name'], $l__directory . $l__filename)) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
	{
		echo 'Error !'.$l__return_form_string;
		exit;
	}
	
	// --- write the header of the CSV file (txt file in fact)----------------------------------------
	header("Expires: 0");
	header("Cache-Control: private");
	header("Pragma: cache");
	header("Content-type: xml/xml");
	header("Content-Disposition: attachment; filename=mm-".time().".xml"); // name of the uploaded file
	// --- end of header------------------------------------------------------------------------------
	
	echo '<?xml version="1.0" encoding="UTF-8"?>
<testsuite name="" >
<node_order><![CDATA[]]></node_order>
<details><![CDATA[]]> 
</details>';
	echo "\n";
	
	$l__buffer = '';
	
	$l__complete_file_name = $l__directory . $l__filename;
	
	$l__ext_id = 1;
	$l__tc_id = 1;
	$l__ts_id = 1;
	
	// ------------------------------------------------------------------------------------------
	function printTestLinkXML($fileName) {
		$dom = new DomDocument();
		$dom->load($fileName);
		$root = $dom->documentElement;
		getElement($root,0,1);
	}
	// ------------------------------------------------------------------------------------------
	
	// ------------------------------------------------------------------------------------------
	function is_sheet($i__node){
		$o__result = True;
		$l__child_nodes = $i__node->childNodes;
		foreach($l__child_nodes as $l__child_node)
		{
			if ($l__child_node->nodeType == XML_ELEMENT_NODE) {
				if($l__child_node->nodeName=='node'){
					$o__result = False;
				}
			}
		}
		return $o__result;
	}
	// ------------------------------------------------------------------------------------------
	
	// ------------------------------------------------------------------------------------------
	function has_steps($i__node)
	{
		return (count(get_steps($i__node))>0) ? True : False;
	}
	function has_icon($i__node,$i__icon)
	{
		$o__result = False;
		$l__child_nodes = $i__node->childNodes;
		foreach($l__child_nodes as $l__child_node)
		{
			if ($l__child_node->nodeType == XML_ELEMENT_NODE) {
				if($l__child_node->nodeName=='icon'){
						if($l__child_node->getAttribute("BUILTIN")==$i__icon)
						{
							$o__result = True;
						}
				}
			}
		}
		return $o__result;
	}
	function has_fire_red($i__node)
	{
		return (has_icon($i__node,'stop')) ? true : false;
	}
	function has_fire_yellow($i__node)
	{
		return (has_icon($i__node,'prepare')) ? true : false;
	}
	function has_fire_green($i__node)
	{
		return (has_icon($i__node,'go')) ? true : false;
	}
	function has_fire($i__node)
	{
		return (has_fire_green($i__node) || has_fire_yellow($i__node) || has_fire_red($i__node)) ? true : false;
	}
	function is_test($i__node)
	{
		return (is_sheet($i__node) || has_steps($i__node)) && has_fire($i__node);
	}
	function is_step($i__node)
	{
		return (has_icon($i__node,'idea')) ? true : false;
	}
	// ------------------------------------------------------------------------------------------
	
	// ------------------------------------------------------------------------------------------
	function get_steps($i__node){
		$o__steps = array();
		$l__child_nodes = $i__node->childNodes;
		foreach($l__child_nodes as $l__child_node)
		{
			if ($l__child_node->nodeType == XML_ELEMENT_NODE) {
				if($l__child_node->nodeName=='node'){
					if(is_step($l__child_node))
					{
						$l__results = '';
						$l__subchild_nodes = $l__child_node->childNodes;
						foreach($l__subchild_nodes as $l__subchild_node)
						{
							if ($l__subchild_node->nodeType == XML_ELEMENT_NODE) {
								if($l__subchild_node->nodeName=='node'){
									$l__results = $l__subchild_node->getAttribute("TEXT");
								}
							}
						}
						$o__steps[] = array(
							'actions'         => $l__child_node->getAttribute("TEXT"),
							'expectedresults' => $l__results
							);
					}
				}
			}
		}
		return $o__steps;
	}
	// ------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------
	 function getElement($dom_element,$i__level,$i__node_order) {
		
		global $l__tc_id;
		global $l__ts_id;
		global $l__ext_id;
		$l__indentation = '';
		for($i=1;$i<=$i__level;$i++)
		{
			$l__indentation .= '	';
		}
		// récupération du nom de l'élément
		$l__node_name = $dom_element->nodeName;

		// récupération de la valeur CDATA, 
		// en supprimant les espaces de formatage.
		$l__textValue = trim($dom_element->firstChild->nodeValue);
		// Récupération de l'attribut TEXT
		if($dom_element->nodeName=='node'){
			$l__name =  $dom_element->getAttribute("TEXT");
			$l__name = str_replace("\"", "''", $l__name);
			if(is_test($dom_element))
			{
				// TEST CASE
				echo "$l__indentation<testcase internalid=\"$l__tc_id\" name=\"$l__name\">\n";
				echo "$l__indentation	<node_order><![CDATA[$i__node_order]]></node_order>\n";
				echo "$l__indentation	<externalid><![CDATA[$l__ext_id]]></externalid>\n";
				echo "$l__indentation	<version><![CDATA[1]]></version>\n";
				echo "$l__indentation	<summary><![CDATA[]]></summary>\n";
				echo "$l__indentation	<preconditions><![CDATA[]]></preconditions>\n";
				echo "$l__indentation	<execution_type><![CDATA[1]]></execution_type>\n";
				echo "$l__indentation	<importance><![CDATA[2]]></importance>\n";
				
				$l__tc_id += 1;
				$l__ext_id += 1;
				
				$l__steps = get_steps($dom_element);
				echo (count($l__steps)>0) ? "$l__indentation	<steps>\n"  : "";
				$l__step_number = 0;
				foreach($l__steps as $l__step)
				{
					$l__step_number += 1;
					echo "$l__indentation		<step>\n";
					echo "$l__indentation			<step_number><![CDATA[$l__step_number]]></step_number>\n";
					echo "$l__indentation			<actions><![CDATA[<p>{$l__step["actions"]}</p>]]></actions>\n";
					echo "$l__indentation			<expectedresults><![CDATA[<p>{$l__step["expectedresults"]}</p>]]></expectedresults>\n";
					echo "$l__indentation			<execution_type><![CDATA[1]]></execution_type>\n";
					echo "$l__indentation		</step>\n";
				}
				echo (count($l__steps)>0) ? "$l__indentation	</steps>\n"  : "";
			}
			elseif(!is_step($dom_element) and has_fire($dom_element))
			{
				// TEST SUITE
				echo "$l__indentation<testsuite name=\"SSP-TS-$l__name\">\n";
				echo "$l__indentation	<node_order><![CDATA[$i__node_order]]></node_order>\n";
				echo "$l__indentation	<details><![CDATA[]]></details>\n";
				
				$l__ts_id += 1;
			}
			$i__node_order += 1;
		}
		if ($dom_element->childNodes->length > 1) {
			$l__node_order = ($i__level==0) ? 1 : 100;
		  foreach($dom_element->childNodes as $dom_child) {
			if ($dom_child->nodeType == XML_ELEMENT_NODE) {
			  $l__node_order = getElement($dom_child,$i__level+1,$l__node_order);
			}
		  }
		}
		if($dom_element->nodeName=='node'){
			if(is_test($dom_element))
			{
				echo "$l__indentation</testcase>\n";
			}
			elseif(!is_step($dom_element) and has_fire($dom_element))
			{
				echo "$l__indentation</testsuite>\n";
			}
		}
		return $i__node_order;
	}
	// ------------------------------------------------------------------------------------------

	printTestLinkXML($l__complete_file_name);
	echo '</testsuite>';
?>

<?php

/*

Create RIS files for journals in JSTOR

*/

//--------------------------------------------------------------------------------------------------
function reference2ris($reference)
{
	$ris = '';
	
	if (isset($reference->genre))
	{
		switch ($reference->genre)
		{
			case 'article':
				$ris .= "TY  - JOUR\n";
				break;
				
			case 'book':
				$ris .= "TY  - BOOK\n";
				break;

			case 'chapter':
				$ris .= "TY  - CHAP\n";
				break;
				
			default:
				$ris .= "TY  - GEN\n";
				break;
		}

	}
	else
	{
		
		if (isset($reference->secondary_title) || isset($reference->issn))
		{
			$ris .= "TY  - JOUR\n";
		}
		else
		{
			$ris .= "TY  - GEN\n";
		}
	}	
	
	if (isset($reference->id))
	{
		$ris .=  "ID  - " . $reference->id . "\n";
	}
	if (isset($reference->publisher_id))
	{
		$ris .=  "ID  - " . $reference->publisher_id . "\n";
	}
	
	if (isset($reference->authors))
	{
		foreach ($reference->authors as $a)
		{
			if (is_object($a))
			{
				$ris .= "AU  - ";
				if (isset($a->forename))
				{
					$ris .= trim($a->forename);
				}
				if (isset($a->lastname))
				{
					$ris .= ' ' . trim($a->lastname);
				}
				if (isset($a->surname))
				{
					$ris .= ' ' . trim($a->surname);
				}
				$ris .= "\n";
			
			}
			else
			{
				$ris .= "AU  - " . trim($a) . "\n";	
			}
		}
	}
	
	if (isset($reference->atitle))
	{
		$ris .=  "TI  - " . strip_tags($reference->atitle) . "\n";
		$ris .=  "JF  - " . strip_tags($reference->title) . "\n";
	}
	else
	{
		$reference->title = str_replace('&quot;',"'", $reference->title);
		$ris .=  "TI  - " . strip_tags($reference->title) . "\n";
	}
	
	if (isset($reference->secondary_title)) 
	{
		switch ($reference->genre)
		{
			case 'chap':
				$ris .=  "T2  - " . $reference->secondary_title . "\n";
				break;
			
			default:
				$ris .=  "JF  - " . $reference->secondary_title . "\n";
				break;
		}
	}
	
	if (isset($reference->secondary_authors))
	{
		foreach ($reference->secondary_authors as $a)
		{
			$ris .= "ED  - " . trim($a) . "\n";	
		}	
	}	
	if (isset($reference->volume)) $ris .=  "VL  - " . $reference->volume . "\n";
	if (isset($reference->issn))
	{
		$ris .=  "SN  - " . $reference->issn . "\n";
	}
	if (isset($reference->issue) && ($reference->issue != ''))
	{
		$ris .=  "IS  - " . $reference->issue . "\n";
	}
	if (isset($reference->spage)) $ris .=  "SP  - " . $reference->spage . "\n";
	if (isset($reference->epage)) $ris .=  "EP  - " . $reference->epage . "\n";
	
	if (isset($reference->date))
	{
		$ris .=  "Y1  - " . str_replace("-", "/", $reference->date) . "\n";
	}
	else
	{
		$ris .=  "Y1  - " . $reference->year . "///\n";
	}
	if (isset($reference->url))
	{
		if (preg_match('/dx.doi.org/', $reference->url))
		{
		}
		elseif (preg_match('/biostor.org/', $reference->url))
		{
		}
		else
		{
			$ris .=  "UR  - " . $reference->url . "\n";
		}
	}
	
	if (isset( $reference->pdf))
	{
		$ris .=  "L1  - " . $reference->pdf . "\n";
	}
	if (isset( $reference->doi))
	{
		$ris .=  "UR  - http://dx.doi.org/" . $reference->doi . "\n";
		// Ingenta
		$ris .= 'M3  - ' . $reference->doi . "\n"; 
		// Mendeley 0.9.9.2
		$ris .=  "DO  - " . $reference->doi . "\n";
	}
	if (isset( $reference->hdl))
	{
		$ris .=  "UR  - http://hdl.handle.net/" . $reference->hdl . "\n";
	}
	if (isset( $reference->biostor))
	{
		$ris .=  "UR  - http://biostor.org/reference/" . $reference->biostor . "\n";
	}

	if (isset( $reference->pmid))
	{
		$ris .=  "UR  - http://www.ncbi.nlm.nih.gov/pubmed/" . $reference->pmid . "\n";
	}
	if (isset( $reference->pmc))
	{
		$ris .=  "UR  - http://www.ncbi.nlm.nih.gov/pmc/articles/PMC" . $reference->pmc . "\n";
	}



	if (isset($reference->abstract))
	{
		$ris .=  "N2  - " . $reference->abstract . "\n";
	}
	
	if (isset($reference->publisher))
	{
		$ris .=  "PB  - " . $reference->publisher . "\n";
	}
	if (isset($reference->publoc))
	{
		$ris .=  "CY  - " . $reference->publoc . "\n";
	}
	
	if (isset($reference->notes))
	{
		$ris .=  "N1  - " . $reference->notes . "\n";
	}
	
	
	if (isset($reference->keywords))
	{
		foreach ($reference->keywords as $keyword)
		{
			$ris .=  "KW  - " . $keyword . "\n";
		}
	}
	
	if (isset($reference->thumbnail))
	{
		$ris .=  "L4  - " . $reference->thumbnail . "\n";
	}	

	
	
	$ris .=  "ER  - \n";
	$ris .=  "\n";
	
	return $ris;
}


//--------------------------------------------------------------------------------------------------
$filename = 'files.txt';

$file_handle = fopen($filename, "r");

while (!feof($file_handle))
{
	$line = trim(fgets($file_handle));
	
	echo $line . "\n";

	if (preg_match('/(?<xmlfilename>bundle\/articles\/(.*).xml)/', $line, $m))
	{
		$xmlfilename = $m['xmlfilename'];
		
		$ris_filename = 'noissn.ris';

		$xml = file_get_contents($xmlfilename);
		
		//echo $xml;

		$dom= new DOMDocument;
		$dom->loadXML($xml);
		$xpath = new DOMXPath($dom);
		
		$reference = new stdclass;
		$reference->genre = 'article';
		
		$nodes = $xpath->query ("/article/id");
		foreach($nodes as $node)
		{
			$reference->id = trim($node->firstChild->nodeValue);
			$reference->url = 'http://www.jstor.org/stable/' . str_replace('10.2307/', '', $reference->id);
		}
		

		$nodes = $xpath->query ("//title");
		foreach($nodes as $node)
		{
			$reference->title = trim($node->firstChild->nodeValue);
		}
		
		$nodes = $xpath->query ("//authors/list-item");
		foreach($nodes as $node)
		{
			$author = '';
			$nc = $xpath->query ("surname", $node);
			foreach ($nc as $n)
			{
				$author = trim($n->firstChild->nodeValue);
			}
			$nc = $xpath->query ("givennames", $node);
			foreach ($nc as $n)
			{
				$author .= ', ' . trim($n->firstChild->nodeValue);
			}
			
			if ($author != '')
			{
				$reference->authors[] = $author;
			}
		}
		

		$nodes = $xpath->query ("//journaltitle");
		foreach($nodes as $node)
		{
			$reference->secondary_title = trim($node->firstChild->nodeValue);
		}
		$nodes = $xpath->query ("//issn");
		foreach($nodes as $node)
		{
			$issn = trim($node->firstChild->nodeValue);
			
			$ris_filename = $issn . '.ris';
			
			$reference->issn = substr($issn, 0, 4) . '-' . substr($issn, 4);
		}
		
		$nodes = $xpath->query ("//volume");
		foreach($nodes as $node)
		{
			$reference->volume = trim($node->firstChild->nodeValue);
		}
		$nodes = $xpath->query ("//issue");
		foreach($nodes as $node)
		{
			$reference->issue = trim($node->firstChild->nodeValue);
		}
		$nodes = $xpath->query ("//fpage");
		foreach($nodes as $node)
		{
			$reference->spage = trim($node->firstChild->nodeValue);
		}
		$nodes = $xpath->query ("//lpage");
		foreach($nodes as $node)
		{
			$reference->epage = trim($node->firstChild->nodeValue);
		}

		$nodes = $xpath->query ("//year");
		foreach($nodes as $node)
		{
			$reference->year = trim($node->firstChild->nodeValue);
		}

		file_put_contents('ris/' . $ris_filename, reference2ris($reference), FILE_APPEND);
	}
}

?>
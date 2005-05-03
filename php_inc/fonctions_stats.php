<?// fonctions statistiques en php

// fonction somme_tableau
// car array_sum ne fonctionne qu'avec des versions + récentes de php
function somme_tableau($tabval) {
if (count($tabval)>0) {
	 foreach ($tabval as $val) $somme=$somme+$val;
	} else $somme=0;
return($somme);
}
// fonction qui renvoie la moyenne des termes d'un tableau
function average($tabval) {
if (count($tabval)>0) { // pour éviter les divissions par 0
	// car array_sum ne fonctionne qu'avec des versions + récentes de php
	//$result=array_sum($tabval)/count($tabval);
	$result=somme_tableau($tabval)/count($tabval);
	}
else $result=0;
return($result);
}

// fonction qui renvoie la variance des termes d'un tableau
function variance($tabval) {
$nbval=count($tabval);
$total=0;
$moy=average($tabval);
foreach ($tabval as $val)
	{
	$total=$total+(($val-$moy)*($val-$moy));
	}
return(($nbval!=0 ? $total/$nbval :0));
}

// fonction qui renvoie l'ecart-type des termes d'un tableau
function ecart_type($tabval) {
	return(sqrt(variance($tabval)));
}
?>


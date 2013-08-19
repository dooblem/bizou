/*Copyright (C) 2013  Baptiste Clement

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

function applyOptimalImagesSize(bestHeight)
{
	var divimages = document.getElementsByClassName("image imagepreview");
	var images = new Array();
	for(var i = 0 ; i < divimages.length ; ++i)
	{
		images.push(divimages[i].children[0].children[0]);
	}
	var newHList = getOptimalHeights(images, bestHeight);
	for( var i = 0 ; i < images.length ; ++i)
	{
		images[i].height = newHList[i];
	}
}

//Renvoi un tuple (with,height) de la taille de la surface d'affichage du navigateur
function getClientWindowSize()
{
	var w = 0;
	var h = 0;
	
	 if (document.body)
	{
		w = (document.body.clientWidth);
		h = (document.body.clientHeight);
	}
	else
	{
		w = (window.innerWidth);
		h = (window.innerHeight);
	}
	
	return [w,h];
}

//renvoie la hauteur optimal de chaque image pour occuper toute la largeur de la surface d'affichage
function getOptimalHeights(images, bestHeight)
{
	var imageHeight = bestHeight;
	var originalSizes = getAllImageSizesForHeight(images, imageHeight);
	var clientSize = getClientWindowSize();
	var newHeights = new Array();
	var allWidth = new Array();
	for(var i = 0 ; i < originalSizes.length ; ++i)
	{
		allWidth.push(originalSizes[i][0]);
	}
	
	var splittedWidth = splitTab(allWidth, clientSize[0]);
	for(var i = 0 ; i < splittedWidth.length ; ++i)
	{
		var sum = 15;
		var ratiosum = 0.0;
		for(var j=0 ; j< splittedWidth[i].length ; ++j)
		{ 
			sum += splittedWidth[i][j] + 5; 
			ratiosum += parseFloat(splittedWidth[i][j]) / parseFloat(imageHeight);
		}
		var deltaHeight = parseInt((clientSize[0] - sum) / parseFloat(ratiosum));
		
		var lastandtoobig = i == (splittedWidth.length-1) && sum < clientSize[0]/2;

		for(var j=0 ; j< splittedWidth[i].length ; ++j)
		{ 
			if(lastandtoobig)
			{
				newHeights.push(bestHeight);
			}
			else
			{
				newHeights.push(imageHeight + deltaHeight);
			}
		}
	}
	return newHeights;
}

//donne la taille de toutes les images affichées
function getAllImageSizes(images)
{
	var sizes = new Array();
	for (var i = 0; i < images.length; i++) {
		sizes.push([images[i].width, images[i].height]);
	}
	return sizes;
}

//donne la taille de toutes les images affichées, redimensionnées pour une hauteur donnée
function getAllImageSizesForHeight(images, height)
{
	var sizes = new Array();
	for (var i = 0; i < images.length; i++) {
		sizes.push([images[i].width / (images[i].height / height) , height]);
	}
	return sizes;
}

//Renvoi un tableau de tableau, correspondant au découpage du tableau d'entrée découpé par tranche de MAX
function splitTab(tab, max)
{
	var splitedTabs = [new Array()];
	var sumSize = 15;
	for (var i = 0; i < tab.length; i++)
	{
		element = tab[i];
		
		var total = sumSize + element + 5;
		//regles arbirtaire de découpage en plus de total > max
		//(parce que ca rend mieux visuellement)
		//=> on ajoute la suite l'élement qui dépasse si sa taille fait moins d'1/4 de max
		//=> ou si l'écart restant représente plus de la moitié de l'élément à ajouter
		if((total > max && (sumSize > max ||(max - sumSize) > (element / 2) || element > max / 4)) || element >= max)
		{
			splitedTabs.push(new Array());
			splitedTabs[splitedTabs.length - 1].push(element);
			sumSize = 16 + element + 4;
		}
		else
		{
			splitedTabs[splitedTabs.length - 1].push(element);
			sumSize = total;
		}
	}	
	return splitedTabs;
}

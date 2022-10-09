<?php
/**
 * @package    Joomla.Plugin
 * @subpackage Content.datatableid
 *
 * @author     Denis Chaissac <denis.chaissac@gmail.com>
 * @copyright  [COPYRIGHT]
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://sgtcoignet.fr
 */

// No direct access to this file
defined('_JEXEC') or die;


use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Language\Text;

// See : https://docs.joomla.org/J4.x:Creating_a_Plugin_for_Joomla
// See : https://docs.joomla.org/J4_Plugin_example_-_Table_of_Contents

/**
 * Plugin to enable loading javascrip init for datables (https://datatables.net/)
 * This uses the 'table id="dataTablesId"' syntax of the HTML table.
 * Adaption of laodmodule
 *
 * @since  4.0
 */

class PlgContentDatatableid extends CMSPlugin
{
  /**
	 * Look for the table.s id and charge javascript with any table id.
	 *
	 * @param   string   $context   The context of the content being passed to the plugin.
	 * @param   object   &$article  The article object.  Note $article->text is also available
	 * @param   mixed    &$params   The article params
	 * @param   integer  $page      The 'page' number
	 *
	 * @return  void
	 *
	 * @since   4.0
	 */

  public function onContentPrepare($context, &$article, &$params, $page = 0)
 	{

 		// Simple performance check to determine whether bot should process further
    // "return" if there is no 'table id=' in the article content

    // Contrôle simple pour déterminer si le bot doit continuer à traiter
    // "return" s'il n'y a pas de 'table id =' dans le contenu de l'article

 		if (stripos($article->text, 'table id=') === false)
 		{
 			return;
 		}

    // the context could be something other than com_content
    // such as a module - in which case do nothing and return
 		// if ($context !== 'com_content.article')
 		// {
 		// 	return;
 		// }
    // Ne pas decommenter si vous voulez qu'il fonctionne dans les vues 'com_content.category" et autres
    // TODO créer une option dans la gestion du plugin

    // If you don't want initialise DataTables on featured articles uncomment below
    // if ($context === 'com_content.featured')
    // {
    //   return;
    // }
    // TODO créer une option dans la gestion du plugin


    // The pattern to search for table id
    // https://lumadis.be/regex/test_regex.php
	     $regexTabId = '#(?:(?:<table(?:[^>]*)) id="([^"]*)")#s';

    // Find all instances of table id and put in $matchestabid
		preg_match_all($regexTabId, $article->text, $matchesTabId, PREG_SET_ORDER, 0);

    // var_dump($matchestabid);
    // $matchestabid[0] contains the text that matched the full pattern
    // $matchestabid[1] contains the id, the text that matched the first captured parenthesized subpattern
    /**
     * TODO Load the language
     */
     $langTag = Factory::getApplication()->getLanguage()->getTag();
     // echo 'Current language is: ' . $langTag ;

     // Source: https://prograide.com/pregunta/77239/comment-detecter-la-langue-actuelle-de-joomla-site-web

    foreach($matchesTabId as $tabId)
    {
      $tabIdList = explode(',', $tabId[1]);
      $dataTableId = trim ($tabIdList[0]);

    // assemble the script with the table id to init DataTable
      $initDataTable = '' ;
      $initDataTable .= '
      $(document).ready(function() {
      $(\'#' . $dataTableId . '\').DataTable({
      language: {

         "sEmptyTable":     "Aucune donnée disponible dans le tableau",
        	"sInfo":           "Affichage de l\'élément _START_ à _END_ sur _TOTAL_ éléments",
        	"sInfoEmpty":      "Affichage de l\'élément 0 à 0 sur 0 élément",
        	"sInfoFiltered":   "(filtré à partir de _MAX_ éléments au total)",
        	"sInfoThousands":  ",",
        	"sLengthMenu":     "Afficher _MENU_ éléments",
        	"sLoadingRecords": "Chargement...",
        	"sProcessing":     "Traitement...",
        	"sSearch":         "Rechercher :",
        	"sZeroRecords":    "Aucun élément correspondant trouvé",
        	"oPaginate": {
        		"sFirst":    "Premier",
        		"sLast":     "Dernier",
        		"sNext":     "Suivant",
        		"sPrevious": "Précédent"
},
        url: "/plugins/content/datatableid/' . $langTag .'.json"
      },
        lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, \''. Text::_('JALL') . '\'] ]
     });
     } );
     ';


  // and here put in the head
  //$document = Factory::getDocument();
  // https://www.dionysopoulos.me/common-mistakes-writing-joomla-plugins.html
    $document = \Joomla\CMS\Factory::getApplication()->getDocument();
    $document->addScriptDeclaration( $initDataTable );

    }

    return;
	}
}

<?php

namespace Drupal\link_stats\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
Use Drupal\node\NodeInterface;
Use DOMDocument;
/**
* Class ExactSalesInvoiceController.
*/
class LinkStatsController extends ControllerBase {

  /**
  	* stats.
  	*
  	* @return markup
    */
  public function stats($node){
  	if ($node) {
  		#Loading current Node
  		if (gettype($node) == 'string' && !$node instanceof NodeInterface) {
  			$node = Node::load($node);
  		}
  		if ($node instanceof NodeInterface) {
  			if ($node->isDefaultRevision() == TRUE) {
			   // Get all of the revision ids.
			   $revision_ids = \Drupal::entityTypeManager()->getStorage('node')->revisionIds($node);
			   $last_revision_id = end($revision_ids);

			   #If its last/current revision
			   #if ($node->getRevisionId() == $last_revision_id) {
         
         #If its last revision
         // if ($last_revision_id) {
        
        #For Current Revision
         if ($last_revision_id = $node->getRevisionId()) {
			     // Load the revision.
			     $last_revision = \Drupal::entityTypeManager()->getStorage('node')->loadRevision($last_revision_id);
			     if ($last_revision instanceof NodeInterface && $last_revision->hasField('body')) {
			     	$bodyy = $last_revision->get('body')->value;
					//Create a new DOM document
					$dom = new DOMDocument;
					@$dom->loadHTML($bodyy);
					$links = $dom->getElementsByTagName('a');
					$totalUrls = count($links);
					//Iterate over the extracted links and display their URLs
					$brokenUrls = 0;
					foreach ($links as $link){
						if ($Url = $link->getAttribute('href')) {
							#Check url is broken or not
							if ($this->checkUrlIsBroken($Url)) {
								$brokenUrls++;
							}
						}
						#$link->nodeValue;#link name
					    #$link->getAttribute('href');#link
					}
					$header = [$this->t("Title"), $this->t("Value")];
					$rows[] = [$this->t("Total Links"), $totalUrls];
					$rows[] = [$this->t("Broken Links"), $brokenUrls];
					#Output a table with results
					$build['table_pager'][] = array(
			          '#type' => 'table',
			          '#header' => $header,
			          '#rows' => $rows,
			      	);
					return $build;
			     }
			   }
			 }else{
        drupal_set_message($this->t("Node default revision is not found!"), "error");
       }
  		}else{
      drupal_set_message($this->t("Node is not available!"), "error");
      }
    }else{
      drupal_set_message($this->t("Node is not found!"), "error");
    }
  	#Output a No data result
    return ['#markup' => $this->t('No data (links) available!')];
  }


  /*
   * Check url is  broken
   */
  public function checkUrlIsBroken($url = null){
  	if ($url) {
  		#add base url if there is no http
  		#for internal urls
  		if (strpos($url, 'http') === false && substr($url, 0, 4) != 'http') {
  			global $base_url;
  			$url = (substr($url, 0,1) == '/'? $base_url.$url : $base_url.'/'.$url);
  		}
  		#Using Curl to check link is broken or Not
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec($curl);

		if ($result === false) {
		    #broken url
		    return true;
		}
		curl_close($curl);
  	}
  	return;
  }

}
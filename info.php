<?php
require_once ('init.php');
use Core\Utils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/** @var Request $request */
if ($request->getMethod() === 'POST') {
    $doi = $request->get('doi');
	if (array_search($doi, [null, ''], true) === false) {
	    $load = Utils::dataLoading($doi);
        if ($load['result']) {
            $render_data = Utils::getRenderDataByLoadedData($load);
            $render_data['doi'] = $doi;
            echo $twig->render('info.twig', $render_data);
        }
	} else {
        Utils::back();
	}
} else {
    (new RedirectResponse('/'))->send();
}
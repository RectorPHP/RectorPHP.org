<?php

declare(strict_types=1);

namespace Rector\Website\Demo\Controller;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Rector\Website\Demo\DemoRunner;
use Rector\Website\Demo\Entity\RectorRun;
use Rector\Website\Demo\Form\DemoFormType;
use Rector\Website\Demo\Form\FormDataFactory\DemoFormDataFactory;
use Rector\Website\Demo\Repository\RectorRunRepository;
use Rector\Website\Demo\ValueObject\DemoFormData;
use Rector\Website\Demo\ValueObject\Option;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class DemoController extends AbstractController
{
    private DemoRunner $demoRunner;

    private RectorRunRepository $rectorRunRepository;

    private DemoFormDataFactory $demoFormDataFactory;
    /**
     * @var string[][]
     */
    private array $demoLinks = [];

    public function __construct(
        RectorRunRepository $rectorRunRepository,
        DemoFormDataFactory $demoFormDataFactory,
        DemoRunner $demoRunner,
        ParameterProvider $parameterProvider
    ) {
        $this->rectorRunRepository = $rectorRunRepository;
        $this->demoRunner = $demoRunner;
        $this->demoLinks = $parameterProvider->provideArrayParameter(Option::DEMO_LINKS);
        $this->demoFormDataFactory = $demoFormDataFactory;
    }

    /**
     * @Route(path="demo/{rectorRun}", name="demo_detail", methods={"GET"})
     * @Route(path="demo", name="demo", methods={"GET", "POST"})
     */
    public function __invoke(Request $request, ?RectorRun $rectorRun = null): Response
    {
        $formData = $this->demoFormDataFactory->createFromRectorRun($rectorRun);

        $demoForm = $this->createForm(DemoFormType::class, $formData, [
            // this is needed for manual render
            'action' => $this->generateUrl('demo'),
        ]);
        $demoForm->handleRequest($request);

        if ($demoForm->isSubmitted() && $demoForm->isValid()) {
            return $this->processFormAndReturnRoute($demoForm);
        }

        return $this->render('demo/demo.twig', [
            'demo_form' => $demoForm->createView(),
            'rector_run' => $rectorRun,
            'demo_links' => $this->demoLinks,
        ]);
    }

    private function processFormAndReturnRoute(FormInterface $form): RedirectResponse
    {
        /** @var DemoFormData $demoFormData */
        $demoFormData = $form->getData();
        $config = $demoFormData->getConfig();

        $rectorRun = $this->createRectorRun($config, $demoFormData);
        $this->demoRunner->runAndPopulateRunResult($rectorRun);

        $this->rectorRunRepository->save($rectorRun);

        return $this->redirectToRoute('demo_detail', [
            'rectorRun' => $rectorRun->getId(),
            '_fragment' => 'result',
        ]);
    }

    private function createRectorRun(string $config, DemoFormData $demoFormData): RectorRun
    {
        return new RectorRun(Uuid::uuid4(), new DateTimeImmutable(), $config, $demoFormData->getContent());
    }
}

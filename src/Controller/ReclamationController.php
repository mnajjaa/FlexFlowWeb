namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Reclamation;
use App\Form\ReclamationType;


class ReclamationController extends AbstractController
{
    // Create a new reclamation
    public function create(Request $request): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('reclamation_show', ['id' => $reclamation->getId()]);
        }

        return $this->render('reclamation/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

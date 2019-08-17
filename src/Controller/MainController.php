namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{
    /**
     * @Route("/", name="status_redirect")
     */
    public function main(Request $request)
    {
        return $this->redirect("https://status.optolith.app", 301);
    }
}
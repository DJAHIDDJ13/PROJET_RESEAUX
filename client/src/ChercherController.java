
import java.io.IOException;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;


public class ChercherController extends Controller {

    @FXML
    void gotoAcceuil(ActionEvent event) throws IOException {
        changeScene(event, "accueil.fxml");
    }

    @FXML
    void gotoChercher(ActionEvent event) throws IOException {
        changeScene(event, "chercher.fxml");
    }

    @FXML
    void gotoProfile(ActionEvent event) throws IOException {
        changeScene(event, "profile.fxml");
    }

    @FXML
    void gotoPropose(ActionEvent event) throws IOException {
        changeScene(event, "propose.fxml");
    }

}


import java.io.IOException;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.TextField;

public class LoginController extends Controller{

    @FXML
    private TextField login_username;

    @FXML
    private TextField login_password;

    @FXML
    void OnSignup(ActionEvent event) throws IOException {
        changeScene(event, "inscription.fxml");
    }

    @FXML
    void onSignin(ActionEvent event) throws IOException {
        changeScene(event, "accueil.fxml");
    }
}


import java.io.IOException;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextField;

public class InscriptionController extends Controller{

    @FXML
    private TextField signup_username;

    @FXML
    private TextField signup_password;

    @FXML
    private PasswordField signup_email;

    @FXML
    void onGoBack(ActionEvent event) throws IOException {
        changeScene(event, "login.fxml");
    }

    @FXML
    void onSignup(ActionEvent event) throws IOException {
        changeScene(event, "accueil.fxml");
    }

}


import java.io.IOException;
import java.util.concurrent.TimeoutException;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.Hyperlink;
import javafx.scene.control.Label;
import javafx.scene.control.TextField;

public class LoginController extends Controller {
    public LoginController() {
        super();
    }
    @FXML
    private TextField login_username;

    @FXML
    private TextField login_password;

    @FXML
    private Label flash_label;

    @FXML
    private Hyperlink flash_link;

    @FXML
    void OnSignup(ActionEvent event) throws IOException {
        changeScene("inscription.fxml", null);
    }
    private String hashed(String s) {
        return s;
    }
    
    private String stripped(String s) {
        return s.replaceAll("\\s+", "");
    }
    

    public String authenticate(String login, String pass) {
        sendMessage("AUTH");
        try {
            if(getResponse(interval, timeout).equals("ACK_AUTH")) {
                sendMessage(stripped(login)+" "+stripped(hashed(pass)));
                String res = getResponse(interval, timeout);
                if(res.equals("AUTH_ACCEPT") == true) {
                    sendMessage("ACK_ACCEPT");
                    username = stripped(login);
                    return "SUCCESS";
                } else {
                    sendMessage("ACK_REFUSE");
                }
            }
        } catch (TimeoutException e) {
            return "CONN_TIMEOUT";
        }
        return "FAILED";
    }
    @FXML
    void onSignin(ActionEvent event) throws IOException {
        String res = authenticate(login_username.getText(), login_password.getText()); 
        switch (res) {
            case "SUCCESS":
                changeScene("acceuil.fxml", null);
                break;
            case "FAILED":
                flash_label.setText("Le username et le mot de passe ne correspondent pas");
                break;
            case "CONN_TIMEOUT":
                flash_label.setText("CONNEXION TIMEOUT...");
                flash_link.setText("retry?");
                flash_link.setOnAction((ActionEvent ev) -> {
                    try {
                        changeScene("login.fxml", null);
                    } catch(IOException e) {
                        System.out.println("Can't change scene");
                    }
                }); break;
            default:
                break;
        }
    }
}

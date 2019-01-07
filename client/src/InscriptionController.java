
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.concurrent.TimeoutException;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.DatePicker;
import javafx.scene.control.Label;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextArea;
import javafx.scene.control.TextField;

public class InscriptionController extends Controller{

    @FXML
    private TextField signup_last_name;

    @FXML
    private TextField signup_first_name;

    @FXML
    private TextField signup_username;

    @FXML
    private PasswordField signup_password;

    @FXML
    private TextField signup_email;

    @FXML
    private TextField signup_tel;

    @FXML
    private TextField signup_birth_place;

    @FXML
    private DatePicker signup_birth_date;

    @FXML
    private TextArea signup_description;
    
    @FXML
    private Label signup_flash;

    @FXML
    void onGoBack(ActionEvent event) throws IOException {
        changeScene("login.fxml", null);
    }
    private String hashed(String s) {
        return s;
    }
    
    private String stringify(String text) {
        if(text.replaceAll("[^A-Za-z0-9_\\-\\.\\ ]", "").equals("")) {
            return "?";
        }
        return text.replaceAll("[^A-Za-z0-9_\\-\\.\\ ]", "");
    }
    
    @FXML
    void onSignup(ActionEvent event) {
        String l_name = signup_last_name.getText();
        String f_name = signup_first_name.getText();
        String u_name = signup_username.getText();
        String pass = signup_password.getText();
        String email = signup_email.getText();
        String tel = signup_tel.getText();
        String birth_place = signup_birth_place.getText();
        SimpleDateFormat date_format = new SimpleDateFormat("yyyy-MM-dd");
        String birth_date;
        if(signup_birth_date.getValue() == null) {
            birth_date = "?";
        }else {
            birth_date = date_format.format(new Date(signup_birth_date.getValue().toEpochDay()));
        }
        String description = signup_description.getText();
        try {
            sendMessage("ADD_USER"); 
            String res = getResponse(interval, timeout);
            if(res.equals("ACK_ADD_USER")) {
                sendMessage(stringify(l_name));
                sendMessage(stringify(f_name));
                sendMessage(stringify(u_name));
                sendMessage(stringify(hashed(pass)));
                sendMessage(stringify(email));
                sendMessage(stringify(tel));
                sendMessage(stringify(birth_place));
                sendMessage(stringify(birth_date));
                sendMessage(stringify(description));
                
                res = getResponse(interval, timeout);
                if(res.equals("VALID_USER_INFO")) {
                    sendMessage("ACK_VALID_USER_INFO");
                    changeScene("login.fxml", null);
                } else {
                    signup_flash.setText("Les information que vous avez rentrer sont pas valides");
                }
            }
        } catch(IOException | TimeoutException e) {
            signup_flash.setText("Connection timeout");
        }
    }

}

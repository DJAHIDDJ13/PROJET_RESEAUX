import java.io.IOException;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.concurrent.TimeoutException;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.Label;
import javafx.scene.text.Text;

public class ProfileController extends Controller {
    
    public String user;
    
    @FXML
    private Label profile_username_city;

    @FXML
    private Text profile_description;

    @FXML
    private Label profile_name;

    @FXML
    private Label profile_first_name;

    @FXML
    private Label profile_birth_place;

    @FXML
    private Label profile_birth_date;

    @FXML
    private Label profile_mail;

    @FXML
    private Label profile_number;

    @FXML
    void gotoAcceuil(ActionEvent event) throws IOException {
        changeScene("acceuil.fxml", null);
    }

    @FXML
    void gotoChercher(ActionEvent event) throws IOException {
        changeScene("chercher.fxml", null);
    }

    @FXML
    void gotoProfile(ActionEvent event) throws IOException {
        changeScene("profile.fxml", null);
    }

    @FXML
    void gotoPropose(ActionEvent event) throws IOException {
        changeScene("propose.fxml", null);
    }

    public void init() {
        UserInfo user_info = getUserInfo();
        if(user_info == null) {
            profile_description.setText("Timeout connection");
            return;
        }
        profile_username_city.setText(user_info.getUsername()+" - "+user_info.getCity());
        profile_description.setText(user_info.getDescription());
        SimpleDateFormat date_format = new SimpleDateFormat("yyyy-MM-dd");
        profile_birth_date.setText(date_format.format(user_info.getDate_birth()));
        profile_birth_place.setText(user_info.getPlace_birth());
        profile_mail.setText(user_info.getEmail());
        profile_number.setText(user_info.getTel());
        profile_name.setText(user_info.getLast_name());
        profile_first_name.setText(user_info.getFirst_name());
    }

    private UserInfo getUserInfo() {
        String username_send = (user != null)? user : username;
        String f_name = null, l_name = null, city = null, description = null, birth_place = null, mail = null ,num = null;
        Date birth_date = null;
        sendMessage("GET_USER_INFO");
        try {
            String res = getResponse(interval, timeout);

            if(res.equals("ACK_GET_USER_INFO")) {
                sendMessage(username_send);
                
                l_name = getResponse(interval, timeout);
                f_name = getResponse(interval, timeout);
                city = getResponse(interval, timeout);
                mail = getResponse(interval, timeout);
                num = getResponse(interval, timeout);
                SimpleDateFormat date_format = new SimpleDateFormat("yyyy-MM-dd");
                birth_date = date_format.parse(getResponse(interval, timeout));
                birth_place = getResponse(interval, timeout);
                description = getResponse(interval, timeout);
                
                sendMessage("ACK_USER_INFO");
            }
        } catch(TimeoutException e) {
            System.out.println("timeout");
            return null;
        } catch(ParseException e) {
            System.out.println("bad date format");
        }
        return new UserInfo(l_name, f_name, username_send, null, city, birth_place, birth_date, mail, num, description);
    }

    void setUser(String GET) {
        user = GET;
    }
}

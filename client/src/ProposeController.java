
import java.awt.event.InputMethodEvent;
import java.awt.event.KeyEvent;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.concurrent.TimeoutException;
import javafx.collections.FXCollections;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.ChoiceBox;
import javafx.scene.control.DatePicker;
import javafx.scene.control.Label;
import javafx.scene.control.TextArea;
import javafx.scene.control.TextField;


public class ProposeController extends Controller {
    @FXML
    private TextField event_title;

    @FXML
    private ChoiceBox<String> event_theme;

    @FXML
    private ChoiceBox<String> event_guest;

    @FXML
    private TextArea event_description;

    @FXML
    private DatePicker event_date;

    @FXML
    private TextField event_hour;
    
    @FXML
    private TextField event_address;

    @FXML
    private TextField event_minute;

    @FXML
    private DatePicker event_deadline;

    @FXML
    private TextField event_capacity;
    
    @FXML
    private Label propose_flash;
    
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
    
    private String stringify(String text) {
        if(text == null)
            return "?";
        if(text.replaceAll("[^A-Za-z0-9_\\-\\.\\ ]", "").equals("")) {
            return "?";
        }
        System.out.println(text);
        return text.replaceAll("[^A-Za-z0-9_\\-\\.\\ ]", "");
    }
    
    @FXML
    void proposeEvent(ActionEvent event) {
        SimpleDateFormat date_format = new SimpleDateFormat("yyyy-MM-dd");
        SimpleDateFormat time_format = new SimpleDateFormat("HH:mm");
        String title = event_title.getText();
        String theme = event_theme.getValue();
        String guest = event_guest.getValue();
        String date;
        if(event_date.getValue() == null)
            date = "?";
        else
            date = date_format.format(new Date(event_date.getValue().toEpochDay()));
        String time = event_hour.getText().replaceAll("[^0-9]","")+":"+event_minute.getText().replaceAll("[^0-9]","");
        String deadline;
        if(event_date.getValue() == null)
            deadline = "?";
        else
            deadline = date_format.format(new Date(event_deadline.getValue().toEpochDay()));
        String capacity = event_capacity.getText().replaceAll("[^0-9]","");
        String description = event_description.getText();
        String address = event_address.getText();
        
        try {
            sendMessage("ADD_EVENT"); 
            String res = getResponse(interval, timeout);
            if(res.equals("ACK_ADD_EVENT")) {
                sendMessage(stringify(title));
                sendMessage(stringify(theme));
                sendMessage(stringify(guest));
                sendMessage(stringify(date));
                sendMessage(stringify(time));
                sendMessage(stringify(deadline));
                sendMessage(stringify(capacity));
                sendMessage(stringify(description));
                sendMessage(stringify(address));
                
                res = getResponse(interval, timeout);
                if(res.equals("VALID_EVENT_INFO")) {
                    sendMessage("ACK_VALID_EVENT_INFO");
                    changeScene("acceuil.fxml", null);
                } else {
                    propose_flash.setText("Les information que vous avez rentrer sont pas valides");
                }
            }
        } catch(IOException | TimeoutException e) {
            propose_flash.setText("Connection timeout");
        }
    }

    void init() {
        System.out.println("INT");
        event_theme.setItems(FXCollections.observableArrayList("Concert", "Exposition", "Festival", "Concours", "Autre"));
        event_guest.setItems(FXCollections.observableArrayList("Tout le Monde", "Mes Amis"));
    }
}

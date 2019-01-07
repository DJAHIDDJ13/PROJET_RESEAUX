
import java.io.IOException;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.concurrent.TimeoutException;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.Label;
import javafx.scene.control.TextArea;
import javafx.scene.layout.GridPane;
import javafx.scene.layout.VBox;
import javafx.scene.text.Text;
import java.util.Date;
import java.util.logging.Level;
import java.util.logging.Logger;

public class EventPageController extends Controller {
    private String event_id;
    @FXML
    private Text page_description;

    @FXML
    private GridPane message_div;

    @FXML
    private TextArea page_message_text_field;

    @FXML
    private VBox page_message_box;

    @FXML
    private Label page_date_time;

    @FXML
    private Label page_theme;

    @FXML
    private Label page_guest;

    @FXML
    private Label page_address;

    @FXML
    private Label page_capacity;
    
    private String stringify(String text) {
        if(text.replaceAll("[^A-Za-z0-9_\\-\\.\\ ]", "").equals("")) {
            return "?";
        }
        return text.replaceAll("[^A-Za-z0-9_\\-\\.\\ ]", "");
    }

    @FXML
    void SendMessage(ActionEvent event) {
        String mes = stringify(page_message_text_field.getText());
        sendMessage("SEND_MESSAGE");
        try {
            String res = getResponse(interval, timeout);
            if(res.equals("ACK_SEND_MESSAGE")) {
                sendMessage(mes);
                res = getResponse(interval, timeout);
                if(res.equals("MESSAGE_SENT")) {
                    show_event_page_info(event_id);
                    page_message_text_field.setText("");
                }
            }
        } catch (TimeoutException ex) {
            try {
                changeScene("login.fxml", null);
            } catch (IOException e) {
                Logger.getLogger(EventPageController.class.getName()).log(Level.SEVERE, null, e);
            }
        }
    }

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

    void init(String event_id) {
        this.event_id = event_id;
        page_message_box.setSpacing(10);
        show_event_page_info(event_id);
    }
    void show_event_page_info(String event_id) {
        sendMessage("GET_EVENT_INFO");
        try {
            String res = getResponse(interval, timeout);
            if(res.equals("ACK_GET_EVENT_INFO")) {
                sendMessage(event_id);
                EventInfo event_info = get_event();
                show_event_info(event_info);
                if(event_info != null) {
                    Message[] messages = get_last_messages();
                    for(Message message: messages) {
                        System.out.println(message.getMessageDiv());
                        page_message_box.getChildren().add(message.getMessageDiv());
                    }
                }
            }
        } catch(TimeoutException e) {
            try {
                changeScene("login.fxml", null);
            } catch (IOException ex) {
                Logger.getLogger(EventPageController.class.getName()).log(Level.SEVERE, null, ex);
            }
        } 
    }

    private Message get_single_message() throws  TimeoutException {
        String writer = getResponse(interval, timeout);
        String date = getResponse(interval, timeout);
        String time = getResponse(interval, timeout);
        String content = getResponse(interval, timeout);
        int seen_num = Integer.parseInt(getResponse(interval, timeout));
        String[] seen = new String[seen_num];
        for(int i=0; i<seen_num; i++) {
            seen[i] = getResponse(interval, timeout);
        }
        Date message_time;
        SimpleDateFormat date_time_format = new SimpleDateFormat("yyyy-MM-dd/HH:mm");
        try {
            message_time = date_time_format.parse(date+"/"+time);
        } catch(ParseException e) {
            message_time = Calendar.getInstance().getTime();
        }
        return new Message(content, message_time, writer, seen, username.equals(writer));
    }
    private Message[] get_last_messages() throws TimeoutException {
        sendMessage("GET_MESSAGES");
        String res = getResponse(interval, timeout);
        int num_messages = Integer.parseInt(res);
        Message[] m = new Message[num_messages];
        for (int i=0; i<num_messages; i++) {
            m[i] = get_single_message();
            sendMessage("ACK_MESSAGE");
        }
        return m;
    }

    private void show_event_info(EventInfo event_info) {
        page_address.setText(event_info.getEvent_address());
        page_capacity.setText(Integer.toString(event_info.getEvent_capacity()));
        SimpleDateFormat date_format = new SimpleDateFormat("yyyy-MM-dd");
        SimpleDateFormat time_format = new SimpleDateFormat("HH:mm");
        page_date_time.setText(date_format.format(event_info.getEvent_date())+" / "+time_format.format(event_info.getEvent_time()));
        page_description.setText(event_info.getEvent_description());
        page_guest.setText(event_info.getEvent_guest());
        page_theme.setText(event_info.getEvent_theme());
    }

}

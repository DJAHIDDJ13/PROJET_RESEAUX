
import java.io.IOException;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.concurrent.TimeoutException;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.DatePicker;
import javafx.scene.control.TextField;
import javafx.scene.layout.VBox;


public class ChercherController extends Controller {
    @FXML
    private TextField username_label;

    @FXML
    private TextField event_name;

    @FXML
    private DatePicker date_start;

    @FXML
    private DatePicker date_end;
    
    @FXML
    private VBox search_result; 
    
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


    @FXML
    void searchEvents(ActionEvent e) {
        search_result.getChildren().clear();
        EventInfo[] events = get_search_results();
        for(EventInfo event: events) {
            search_result.getChildren().add(event.get_div_node(this));
        }
    }

    private EventInfo[] get_search_results() {
        String res;
        EventInfo[] ret = null;
        sendMessage("GET_SEARCH_EVENTS");
        String user = stringify(username_label.getText());
        String event = stringify(event_name.getText());
        String start = getDate(date_start);
        String end = getDate(date_end);
        try {
            res = getResponse(interval, timeout);
            if(res.equals("ACK_GET_SEARCH_EVENTS")){
                sendMessage(user+" "+event+" "+start+" "+end);
                res = getResponse(interval, timeout);
                if(res.equals("VALID_SEARCH_QUERY")) {
                    sendMessage("GET_EVENTS_NUM");
                    res = getResponse(interval, timeout);
                    int n = Integer.parseInt(res);
                    ret = new EventInfo[n];
                    for(int i=0; i<n; i++) {
                        ret[i] = get_event();
                    }
                    res = getResponse(interval, timeout);
                    if(!res.equals("END_EVENTS")) {
                        System.out.println("Too many events received");
                    }
                } else {
                    System.out.println("INVALID SEARCH QUERY");
                    
                }
            }
        } catch(TimeoutException e) {
           System.out.println("timeout");
        }
        return ret;
    }
    private String getDate(DatePicker d) {
        DateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd");
        if(d.getValue() == null)
            return "?";
        return dateFormat.format(new Date(d.getValue().toEpochDay()));
    }
    private String stringify(String text) {
        if(text == null) {
            return "?";
        }
        return text.replaceAll("[^A-Za-z0-9_\\-.]", "");
    }
}

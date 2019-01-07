
import java.io.IOException;
import java.util.concurrent.TimeoutException;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.layout.VBox;

public class MainController extends Controller {
    @FXML
    private VBox vbox;
    
    @FXML
    void gotoAcceuil(ActionEvent event) throws IOException {
        changeScene("accueil.fxml", null);
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
        EventInfo[] events = get_recent_event_info();
        for(EventInfo event: events) {
            vbox.getChildren().add(event.get_div_node(this));
        }
    }
    
    
    public EventInfo[] get_recent_event_info() {
        String res;
        EventInfo[] ret = null;
        sendMessage("GET_RECENT_EVENTS");
        try {
            res = getResponse(interval, timeout);
            if(res.equals("ACK_GET_RECENT_EVENTS")){
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
            }
        } catch(TimeoutException e) {
           System.out.println("timeout");
        }
        return ret;
    }

}


import java.text.SimpleDateFormat;
import java.util.Arrays;
import java.util.Date;
import javafx.scene.control.Label;
import javafx.scene.layout.GridPane;
import javafx.scene.text.Text;


public class Message {
    private String message_content;
    private Date message_time;
    private String message_user;
    private String[] message_seen;
    private boolean message_own;

    public Message(String message_content, Date message_time, String message_user, String[] message_seen, boolean message_own) {
        this.message_content = message_content;
        this.message_time = message_time;
        this.message_user = message_user;
        this.message_seen = message_seen;
        this.message_own = message_own;
    }
    
    public GridPane getMessageDiv() {
        Text text_box = new Text(message_content);
        SimpleDateFormat date_time_format = new SimpleDateFormat("yyyy-MM-dd/HH:mm");
        Label time_date = new Label(date_time_format.format(message_time));        
        Label user_name = new Label(message_user);        
        Label seen = new Label((message_seen.length == 0 )? "": "seen by: "+String.join(", ", Arrays.asList(message_seen)));

        GridPane gridPane = new GridPane();
        gridPane.add(text_box,  0, 0, 1, 1);
        gridPane.add(user_name, 0, 1, 1, 1);
        gridPane.add(seen,      1, (message_own)?0:1, 1, 1);
        gridPane.add(time_date, 1,(!message_own)?0:1, 1, 1);
        gridPane.setHgap(30);
        gridPane.setStyle("-fx-background-color: "+((message_own)?"#F5F5F5":"FBFBFB")+";");
        return gridPane;
    }
}

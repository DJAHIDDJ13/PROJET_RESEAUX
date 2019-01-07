
import java.io.IOException;
import java.util.Date;
import javafx.event.ActionEvent;
import javafx.event.EventHandler;
import javafx.geometry.HPos;
import javafx.scene.Node;
import javafx.scene.control.Button;
import javafx.scene.control.Hyperlink;
import javafx.scene.control.Label;
import javafx.scene.layout.ColumnConstraints;
import javafx.scene.layout.GridPane;
import javafx.stage.Stage;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author djahi
 */
public class EventInfo {
    private final int event_id;
    private final Date event_date;
    private final Date event_time;
    private final Date event_deadline;
    private final String event_title;
    private final String event_address;
    private final String event_city;
    private final String event_description;
    private final int event_capacity;
    private final String event_organizer;
    private final String event_theme;
    private final String event_guest;
    private final String event_state;

    public EventInfo(int event_id, Date event_date, Date event_time, Date event_deadline, String event_title, String event_address, String event_city, String event_description, int event_capacity, String event_organizer, String event_theme, String event_guest, String event_state) {
        this.event_id = event_id;
        this.event_title = event_title;
        this.event_city = event_city;
        this.event_address = event_address;
        this.event_date = event_date;
        this.event_time = event_time;
        this.event_deadline = event_deadline;
        this.event_organizer = event_organizer;
        this.event_description = event_description;
        this.event_capacity = event_capacity;
        this.event_theme = event_theme;
        this.event_guest = event_guest;
        this.event_state = event_state;
    }
    
    public Node get_div_node(Controller controller) {
        GridPane gridPane = new GridPane();
        Hyperlink title = new Hyperlink(event_title);
        title.setOnAction((ActionEvent t) -> {
            try {
                controller.changeScene("event_page.fxml", Integer.toString(event_id));
            } catch(IOException e) {
                System.out.println(Integer.toString(event_id));
            }
        });
        Label city = new Label(event_city);
        Hyperlink organizer = new Hyperlink(event_organizer);
        organizer.setOnAction((ActionEvent t)->{
            try {
                controller.changeScene("profile.fxml",  organizer.getText());
            } catch(IOException e) {
                System.out.println("ioexception");
            }
        });
        String button_text = (event_state.equals("PARTICIPATING"))? "Abandoner" : (event_state.equals("NOT_PARTICIPATING"))? "Participer" : "---------";
        Button participate = new Button(button_text);
        
        gridPane.add(title, 0, 0);
        gridPane.add(city, 1, 0);
        gridPane.add(organizer, 2, 0);
        gridPane.add(participate, 3, 0);
        
        ColumnConstraints col1 = new ColumnConstraints();
        col1.setPercentWidth(25);
        gridPane.getColumnConstraints().addAll(col1,col1,col1,col1);

        return gridPane;
    }

    public int getEvent_id() {
        return event_id;
    }

    public Date getEvent_date() {
        return event_date;
    }

    public Date getEvent_time() {
        return event_time;
    }

    public Date getEvent_deadline() {
        return event_deadline;
    }

    public String getEvent_title() {
        return event_title;
    }

    public String getEvent_address() {
        return event_address;
    }

    public String getEvent_city() {
        return event_city;
    }

    public String getEvent_description() {
        return event_description;
    }

    public int getEvent_capacity() {
        return event_capacity;
    }

    public String getEvent_organizer() {
        return event_organizer;
    }

    public String getEvent_theme() {
        return event_theme;
    }

    public String getEvent_guest() {
        return event_guest;
    }

    public String getEvent_state() {
        return event_state;
    }
    
}

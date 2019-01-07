
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.Socket;
import java.net.UnknownHostException;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.concurrent.TimeoutException;
import java.util.logging.Level;
import java.util.logging.Logger;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.stage.Stage;

public class Controller {
    private final int port = 8080;
    private final String hostname = "127.0.0.1";
    
    public final int timeout = 1000;
    public final int interval = 100;

    private Socket server;
    private PrintWriter out;
    private BufferedReader in;
    public Stage window;
    public String username;
    public void connect() {
        try {
            server = new Socket(hostname, port);
            out = new PrintWriter(server.getOutputStream(), true);
            in = new BufferedReader(new InputStreamReader(server.getInputStream()));
        } catch(UnknownHostException e) {
            System.out.println("unknown host exception");
        } catch(IOException e) {
            System.out.println("IO exception");
        }
    }
    
    public EventInfo get_event() {
        String id, title, address, city, description, capacity, time = null, date = null, theme, guest, organizer, deadline, state;
        EventInfo res = null;
        SimpleDateFormat date_format = new SimpleDateFormat("yyyy-MM-dd");
        SimpleDateFormat time_format = new SimpleDateFormat("HH:mm");
        try {
            id = getResponse(interval, timeout);
            title = getResponse(interval, timeout);
            organizer = getResponse(interval, timeout);
            city = getResponse(interval, timeout);
            date = getResponse(interval, timeout);
            time = getResponse(interval, timeout);
            deadline = getResponse(interval, timeout);
            description = getResponse(interval, timeout);
            address = getResponse(interval, timeout);
            capacity = getResponse(interval, timeout);
            theme = getResponse(interval, timeout);
            guest = getResponse(interval, timeout);
            state = getResponse(interval, timeout);
            res = new EventInfo(Integer.parseInt(id), date_format.parse(date), time_format.parse(time), date_format.parse(deadline), title, address, city, description, Integer.parseInt(capacity), organizer, theme, guest, state);
            sendMessage("ACK_EVENT");
        } catch(TimeoutException e) {
            System.out.println("timeout");
        } catch(ParseException e) {
            System.out.println(date+" *** "+time);
        }
        return res;
    }

    public void setData(Stage window, String username, Socket server, PrintWriter out, BufferedReader in) {
        this.username = username;
        this.window = window;
        this.server = server;
        this.out = out;
        this.in = in;
    } 
    public void setData(Stage window) {
        this.window = window;
    }
    public void changeScene(String s, String GET) throws IOException {
        FXMLLoader loader = new FXMLLoader(getClass().getResource(s));
        
        Scene loginScene = new Scene(loader.load());
        loader.<Controller>getController().setData(window, username, server, out, in);
        switch (s) {
            case "acceuil.fxml":
                loader.<MainController>getController().init();
                break;
            case "profile.fxml":
                if(GET != null) {
                    loader.<ProfileController>getController().setUser(GET);
                    loader.<ProfileController>getController().init();
                } else {
                    loader.<ProfileController>getController().init();
                }   break;
            case "propose.fxml":
                loader.<ProposeController>getController().init();            
                break;
            case "event_page.fxml":
                loader.<EventPageController>getController().init(GET);
                break;
            default:
                break;
        }
        window.setScene(loginScene);
        window.show();
    }
    
    public String getResponse(int interval, int timeout) throws TimeoutException {
        String s = "";
        long t0 = System.currentTimeMillis();
        
        while(s.equals("") && System.currentTimeMillis() - t0 < timeout) {
            try {
                s = in.readLine();
                Thread.sleep(interval);
            } catch(IOException  e) {
                    System.out.println("IOException");
            } catch (InterruptedException ex) {
                Logger.getLogger(Controller.class.getName()).log(Level.SEVERE, null, ex);
            }
        }
        if(System.currentTimeMillis() - t0 > timeout) {
            throw new TimeoutException();
        }
        return s;
    }
    
    public void sendMessage(String mes) {
        out.println(mes);
    }
}

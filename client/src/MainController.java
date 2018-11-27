
import java.io.IOException;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;

public class MainController extends Controller {
    
    @FXML
    void onSubmitClick(ActionEvent event) throws IOException {
        changeScene(event, "login.fxml");
    }
}

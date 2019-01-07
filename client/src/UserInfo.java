
import java.util.Date;


public class UserInfo {

    public String getLast_name() {
        return last_name;
    }

    public String getFirst_name() {
        return first_name;
    }

    public String getUsername() {
        return username;
    }

    public String getCity() {
        return city;
    }

    public String getPlace_birth() {
        return place_birth;
    }

    public Date getDate_birth() {
        return date_birth;
    }

    public String getEmail() {
        return email;
    }

    public String getTel() {
        return tel;
    }

    public String getDescription() {
        return description;
    }
    private String last_name;
    private String first_name;
    private String username;
    private String password;
    private String city;
    private String place_birth;
    private Date date_birth;
    private String email;
    private String tel;
    private String description;

    public UserInfo(String last_name, String first_name, String username, String password, String city, String place_birth, Date date_birth, String email, String tel, String description) {
        this.last_name = last_name;
        this.first_name = first_name;
        this.username = username;
        this.password = password;
        this.city = city;
        this.place_birth = place_birth;
        this.date_birth = date_birth;
        this.email = email;
        this.tel = tel;
        this.description = description;
    } 
}

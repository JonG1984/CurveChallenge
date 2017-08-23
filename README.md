# Curve Test
The service has one endpoint, /users which accpets only GET requests.  
The endpoint accepts one QueryString Parameter of "userIds".
"userIds" should contain two comma seperated ids.
The service uses test data, which can be found in the data/repositories.json.  Feel free to modify the data
in order to test longer/shorter hop distances.
 
A response will be returned indicating how many hops it took, and which users it went through.
 
# Valid Paths 
http://localhost:8888/users?userIds=user1,user11
http://localhost:8888/users?userIds=user1,user99
http://localhost:8888/users?userIds=user1,user4

# Invalid Paths:
http://localhost:8888/users?userIds=user1,user1111
const { MongoClient } = require('mongodb');

async function connectDB() {
  const uri = "mongodb://localhost:27017/mydatabase"; 
  const client = new MongoClient(uri, { useNewUrlParser: true, useUnifiedTopology: true });
  
  try {
    await client.connect();
    console.log("Connected to MongoDB!");

    // List all databases (example operation)
    const databasesList = await client.db().admin().listDatabases();
    console.log("Databases:");
    databasesList.databases.forEach(db => console.log(` - ${db.name}`));
    
    // Perform additional operations here
    // For example, you can keep the connection open for an Express server or other tasks

  } catch (error) {
    console.error("Error connecting to MongoDB:", error);
  }
  // Note: Not closing the connection immediately allows you to use it later.
}

// Call the function to connect
connectDB();



import { db } from './index.js'
import { getDocs, collection } from 'firebase/firestore'
import { Loading, Notify } from 'quasar'

const listProductsDocuments = async (collectionName) => {
  return new Promise((resolve, reject) => {
    // Loading.show()

    try {
      getDocs(collection(db, collectionName)).then((querySnapshot) => {
        const documents = []
        const result = {}
        const capacities = []
        querySnapshot.forEach((document) => {
          documents.push({ id: document.id, ...document.data() })
        })
        
        // Document Level Results
        documents.forEach((el, index) => {
          result[el.name] = {
            id: el.id,
            name: el.name,
            type: el.type, //NON-INVERTER or INVERTER
            origin: el.origin,
            features: el.features,
            minStock: el.minStock,
            capacity: el.capacity
          }
          capacities.push({
            name: el.name,
            capcity: el.capacity,
            srp: el.srp
          })
        })

        // Result Level to be resolved
        for(const i in result){
          result[i].capacities = capacities.filter(el => el.name === i)
        }

        resolve(result)
      })
    } catch (err) {
      Loading.hide()
      Notify.create({
        type: 'negative',
        message: err.message
      })
      reject(err.message)
    }
  })
}

export default listProductsDocuments